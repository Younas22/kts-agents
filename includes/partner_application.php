<?php
/**
 * Partner application workflow: checks → insert pending agent → emails.
 */

declare(strict_types=1);

final class DuplicateEmailException extends RuntimeException
{
}

/**
 * Process a POSTed application.
 *
 * @return array{status: int, payload: array} HTTP status + JSON-ready payload
 */
function handle_partner_submission(array $input): array
{
    // 1) Rate limit per IP
    $limit = config('rate_limit');
    if (!rate_limit_hit('partner-apply|' . client_ip(), $limit['max_attempts'], $limit['window'])) {
        log_warning('Partner application rate limited', ['ip' => client_ip()]);
        return ['status' => 429, 'payload' => ['ok' => false, 'message' => MSG_RATE_LIMIT]];
    }

    // 2) CSRF
    if (!csrf_verify($input['_token'] ?? null)) {
        return ['status' => 403, 'payload' => [
            'ok'         => false,
            'message'    => MSG_CSRF,
            'csrf_token' => csrf_token(),
        ]];
    }

    // 3) Honeypot: the page script always empties this field, so a value means a bot
    //    (or a no-JS browser that autofilled it). Never fake success – show an error instead.
    if (clean_text($input['kt_hp_check'] ?? '') !== '') {
        log_warning('Partner application honeypot triggered – nothing saved', [
            'ip'    => client_ip(),
            'email' => mask_email(clean_text($input['email'] ?? '')),
        ]);
        return ['status' => 422, 'payload' => ['ok' => false, 'message' => MSG_SERVER_ERROR]];
    }

    // 4) Validation
    [$data, $errors] = validate_partner_application($input);

    // 5) CAPTCHA (only checked once the form itself is valid, so a solved widget isn't wasted)
    if (!$errors && !captcha_verify($input['frc-captcha-response'] ?? null)) {
        $errors['captcha'] = 'Please complete the verification and try again.';
    }

    if ($errors) {
        return ['status' => 422, 'payload' => ['ok' => false, 'message' => MSG_VALIDATION, 'errors' => $errors]];
    }

    // 6) Duplicate email + 7) insert
    try {
        $application = create_partner_application($data);
    } catch (DuplicateEmailException) {
        return ['status' => 409, 'payload' => [
            'ok'      => false,
            'message' => MSG_DUPLICATE,
            'errors'  => ['email' => 'This email address is already registered or has an existing application.'],
        ]];
    } catch (Throwable $e) {
        log_error('Partner application could not be saved', ['exception' => $e, 'email' => mask_email($data['email'])]);
        return ['status' => 500, 'payload' => ['ok' => false, 'message' => MSG_SERVER_ERROR]];
    }

    log_info('Partner application created', [
        'user_id'    => $application['id'],
        'agent_code' => $application['agent_code'],
        'email'      => mask_email($application['email']),
    ]);

    // 8) Notifications (never fail the request – the application is saved)
    try {
        send_partner_application_emails($application);
    } catch (Throwable $e) {
        log_error('Partner application emails failed', ['exception' => $e, 'user_id' => $application['id']]);
    }

    return ['status' => 200, 'payload' => ['ok' => true]];
}

function email_is_registered(PDO $pdo, string $email): bool
{
    // users.email uses a case-insensitive collation, so this also catches case variants.
    $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    return (bool) $stmt->fetchColumn();
}

/**
 * Next code in the format the existing system uses (AGT-0001, AGT-0002, ...).
 */
function next_agent_code(PDO $pdo): string
{
    $max = $pdo->query(
        "SELECT MAX(CAST(SUBSTRING(agent_code, 5) AS UNSIGNED)) FROM users WHERE agent_code LIKE 'AGT-%'"
    )->fetchColumn();

    return 'AGT-' . str_pad((string) ((int) $max + 1), 4, '0', STR_PAD_LEFT);
}

/**
 * Insert the applicant as a pending, inactive agent.
 *
 * The account cannot be used to log in: its password is a bcrypt hash of a random
 * secret that is immediately discarded (never stored, logged or emailed). Access is
 * granted only after an admin approves the agent and a password is set separately.
 *
 * @throws DuplicateEmailException
 * @return array{id: int, agent_code: string, company_name: string, first_name: string, last_name: string,
 *               email: string, previous_contact: string, previous_contact_label: string, submitted_at: string}
 */
function create_partner_application(array $data): array
{
    $pdo = db();

    if (email_is_registered($pdo, $data['email'])) {
        throw new DuplicateEmailException();
    }

    $now          = date('Y-m-d H:i:s');
    $unusableHash = password_hash(bin2hex(random_bytes(32)), PASSWORD_BCRYPT, ['cost' => 12]);

    $sql = 'INSERT INTO users (
                user_type, first_name, last_name, company_name, previous_contact, email, phone,
                agent_code, approval_status, status, password, internal_notes, created_at, updated_at
            ) VALUES (
                \'agent\', :first_name, :last_name, :company_name, :previous_contact, :email, :phone,
                :agent_code, \'pending\', \'inactive\', :password, :internal_notes, :created_at, :updated_at
            )';

    // Retry if another request took the same agent code at the same moment.
    for ($attempt = 1; ; $attempt++) {
        $agentCode = next_agent_code($pdo);

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':first_name'       => $data['first_name'],
                ':last_name'        => $data['last_name'],
                ':company_name'     => $data['company_name'],
                ':previous_contact' => $data['previous_contact'],
                ':email'            => $data['email'],
                ':phone'            => $data['phone'],
                ':agent_code'       => $agentCode,
                ':password'         => $unusableHash,
                ':internal_notes'   => 'Applied via the B2B partner registration page on ' . $now . ' (' . config('app.timezone') . ').',
                ':created_at'       => $now,
                ':updated_at'       => $now,
            ]);
            break;
        } catch (PDOException $e) {
            if (is_duplicate_key_error($e, 'users_email_unique')) {
                throw new DuplicateEmailException();
            }
            if ($attempt < 5 && is_duplicate_key_error($e, 'users_agent_code_unique')) {
                continue;
            }
            throw $e;
        }
    }

    return [
        'id'                     => (int) $pdo->lastInsertId(),
        'agent_code'             => $agentCode,
        'company_name'           => $data['company_name'],
        'first_name'             => $data['first_name'],
        'last_name'              => $data['last_name'],
        'email'                  => $data['email'],
        'phone'                  => $data['phone'],
        'previous_contact'       => $data['previous_contact'],
        'previous_contact_label' => previous_contact_label($data['previous_contact']),
        'submitted_at'           => date('j M Y, H:i') . ' ' . config('app.timezone'),
    ];
}
