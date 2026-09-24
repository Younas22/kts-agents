<?php
/**
 * Reusable transactional email service using the Resend HTTP API.
 * https://resend.com/docs/api-reference/emails/send-email
 */

declare(strict_types=1);

/**
 * Send one email through Resend.
 *
 * @param array{
 *   to: string|string[],
 *   subject: string,
 *   html: string,
 *   text?: string,
 *   reply_to?: string|string[],
 *   idempotency_key?: string,
 *   attachments?: array<int, array{filename: string, content: string, content_type?: string, content_id?: string}>
 * } $message   attachment `content` is base64; `content_id` makes it an inline image (cid:...)
 * @return array{ok: bool, id?: string, error?: string}
 */
function send_mail(array $message): array
{
    $to = array_values(array_filter((array) $message['to']));

    if (config('mail.preview')) {
        write_mail_preview($to, $message['subject'], $message['html'], $message['attachments'] ?? []);
    }

    $apiKey = (string) config('mail.resend_api_key');
    $from   = (string) config('mail.from');

    if ($apiKey === '' || $from === '') {
        log_warning('Email not sent: RESEND_API_KEY or MAIL_FROM is not configured', ['subject' => $message['subject']]);
        return ['ok' => false, 'error' => 'not_configured'];
    }

    $payload = [
        'from'    => sprintf('%s <%s>', str_replace(['"', '<', '>', "\r", "\n"], '', (string) config('mail.from_name')), $from),
        'to'      => $to,
        'subject' => $message['subject'],
        'html'    => $message['html'],
    ];
    if (!empty($message['text'])) {
        $payload['text'] = $message['text'];
    }
    if (!empty($message['reply_to'])) {
        $payload['reply_to'] = array_values(array_filter((array) $message['reply_to']));
    }
    if (!empty($message['attachments'])) {
        $payload['attachments'] = $message['attachments'];
    }

    $headers = [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    if (!empty($message['idempotency_key'])) {
        // Resend ignores repeats of the same key for 24h, so a retry can never send twice.
        $headers[] = 'Idempotency-Key: ' . $message['idempotency_key'];
    }

    $ch = curl_init(config('mail.resend_api_url') . '/emails');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
    ]);

    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error  = curl_error($ch);
    curl_close($ch);

    $json = is_string($body) ? json_decode($body, true) : null;

    if ($body === false || $status < 200 || $status >= 300) {
        log_error('Resend email failed', [
            'subject'    => $message['subject'],
            'status'     => $status,
            'curl_error' => $error ?: null,
            'resend'     => is_array($json) ? ($json['message'] ?? $json['name'] ?? null) : null,
        ]);
        return ['ok' => false, 'error' => 'send_failed'];
    }

    log_info('Email sent', ['subject' => $message['subject'], 'id' => $json['id'] ?? null]);
    return ['ok' => true, 'id' => (string) ($json['id'] ?? '')];
}

/**
 * Render an email template from /emails with the given variables.
 */
function render_email(string $template, array $vars): string
{
    $file = APP_ROOT . '/emails/' . basename($template) . '.php';

    return (static function (string $__file, array $__vars): string {
        extract($__vars, EXTR_SKIP);
        ob_start();
        require $__file;
        return (string) ob_get_clean();
    })($file, $vars);
}

/** Local development helper: keep a copy of each email in storage/mail-preview. */
function write_mail_preview(array $to, string $subject, string $html, array $attachments = []): void
{
    // Show inline (cid:) images in the browser preview.
    foreach ($attachments as $file) {
        if (!empty($file['content_id'])) {
            $dataUri = 'data:' . ($file['content_type'] ?? 'image/png') . ';base64,' . $file['content'];
            $html    = str_replace('cid:' . $file['content_id'], $dataUri, $html);
        }
    }

    $dir = STORAGE_PATH . '/mail-preview';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $name = date('Ymd-His') . '-' . substr(preg_replace('/[^a-z0-9]+/', '-', strtolower($subject)) ?? 'email', 0, 60) . '.html';
    $meta = '<!-- To: ' . e(implode(', ', $to)) . ' | Subject: ' . e($subject) . " -->\n";
    @file_put_contents($dir . '/' . $name, $meta . $html);
}

/**
 * Send the admin notification and the applicant confirmation.
 * Failures are logged only – the application is already saved at this point.
 */
function send_partner_application_emails(array $application): void
{
    // Logo: public LOGO_URL if configured, otherwise embed the local file inline (cid:),
    // which works in Gmail/Outlook/Apple Mail without a publicly reachable URL.
    $logoUrl     = (string) config('mail.logo_url');
    $attachments = [];
    $logoFile    = APP_ROOT . '/assets/images/khan-travel-logo-email.png';
    if ($logoUrl === '' && is_file($logoFile)) {
        $logoUrl       = 'cid:khan-travel-logo';
        $attachments[] = [
            'filename'     => 'khan-travel-logo.png',
            'content'      => base64_encode((string) file_get_contents($logoFile)),
            'content_type' => 'image/png',
            'content_id'   => 'khan-travel-logo',
        ];
    }

    $common = [
        'logo_url'     => $logoUrl,
        'app_url'      => absolute_url('become-a-partner'),
        'main_site'    => (string) config('app.main_site_url'),
        'year'         => date('Y'),
    ];

    // 1) Admin notification
    $adminRecipients = array_filter(array_map('trim', explode(',', (string) config('mail.admin_email'))));
    if ($adminRecipients) {
        $reviewUrl = (string) config('mail.admin_review_url');
        $vars = $common + [
            'application' => $application,
            'review_url'  => $reviewUrl !== '' ? str_replace('{id}', (string) $application['id'], $reviewUrl) : '',
        ];

        send_mail([
            'to'              => $adminRecipients,
            'subject'         => 'New Khan Travel B2B Partner Application',
            'html'            => render_email('admin-new-partner', $vars),
            'text'            => render_email('admin-new-partner.txt', $vars),
            'reply_to'        => $application['email'],
            'idempotency_key' => 'partner-admin-' . $application['id'],
            'attachments'     => $attachments,
        ]);
    } else {
        log_warning('ADMIN_EMAIL is not configured – admin notification skipped');
    }

    // 2) Applicant confirmation
    $vars = $common + ['application' => $application];

    send_mail([
        'to'              => $application['email'],
        'subject'         => 'We Received Your Khan Travel B2B Partner Application',
        'html'            => render_email('partner-confirmation', $vars),
        'text'            => render_email('partner-confirmation.txt', $vars),
        'reply_to'        => (string) config('mail.reply_to'),
        'idempotency_key' => 'partner-confirmation-' . $application['id'],
        'attachments'     => $attachments,
    ]);
}
