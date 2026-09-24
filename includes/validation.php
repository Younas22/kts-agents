<?php
/**
 * Server-side validation for the partner application form.
 * Messages come from the translation files (validation.*).
 */

declare(strict_types=1);

/** Allowed "previous contact" values (stored in users.previous_contact). Labels: form.previous_contact.options.* */
const PREVIOUS_CONTACT_OPTIONS = ['none', 'sales_team', 'business_development', 'support_team', 'other'];

function previous_contact_label(?string $key, ?string $lang = null): string
{
    return in_array($key, PREVIOUS_CONTACT_OPTIONS, true)
        ? (string) t('form.previous_contact.options.' . $key, [], $lang)
        : 'Not specified';
}

/** Trim, remove control/invisible characters and collapse internal whitespace. */
function clean_text(mixed $value): string
{
    if (!is_string($value)) {
        return '';
    }
    $value = preg_replace('/[\p{Cc}\p{Cf}]+/u', ' ', $value) ?? '';
    $value = preg_replace('/\s+/u', ' ', $value) ?? '';
    return trim($value);
}

/**
 * Validate and normalise the submitted form.
 *
 * @return array{0: array<string, string>, 1: array<string, string>} [clean data, field errors]
 */
function validate_partner_application(array $input): array
{
    $data = [
        'company_name'     => clean_text($input['company_name'] ?? ''),
        'first_name'       => clean_text($input['first_name'] ?? ''),
        'last_name'        => clean_text($input['last_name'] ?? ''),
        'email'            => mb_strtolower(clean_text($input['email'] ?? '')),
        'phone'            => clean_text($input['phone'] ?? ''),
        'previous_contact' => clean_text($input['previous_contact'] ?? ''),
    ];
    $errors = [];

    $namePattern = "/^[\p{L}\p{M}][\p{L}\p{M} '’.\-]*$/u";

    // Company
    $len = mb_strlen($data['company_name']);
    if ($len === 0) {
        $errors['company_name'] = t('validation.company_required');
    } elseif ($len < 2 || $len > 255) {
        $errors['company_name'] = t('validation.company_length');
    } elseif (preg_match('/[<>{}]/', $data['company_name'])) {
        $errors['company_name'] = t('validation.company_chars');
    }

    // First / last name
    foreach (['first_name', 'last_name'] as $field) {
        $len = mb_strlen($data[$field]);
        if ($len === 0) {
            $errors[$field] = t("validation.{$field}_required");
        } elseif ($len > 100) {
            $errors[$field] = t("validation.{$field}_length");
        } elseif (!preg_match($namePattern, $data[$field])) {
            $errors[$field] = t('validation.name_chars');
        }
    }

    // Email
    if ($data['email'] === '') {
        $errors['email'] = t('validation.email_required');
    } elseif (mb_strlen($data['email']) > 254 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = t('validation.email_invalid');
    }

    // Phone: digits with optional +, spaces, dashes, dots and brackets; 7–15 digits (E.164 max).
    $digits = preg_replace('/\D+/', '', $data['phone']) ?? '';
    if ($data['phone'] === '') {
        $errors['phone'] = t('validation.phone_required');
    } elseif (mb_strlen($data['phone']) > 30 || !preg_match('/^\+?[0-9\s().\-]+$/', $data['phone'])
        || strlen($digits) < 7 || strlen($digits) > 15) {
        $errors['phone'] = t('validation.phone_invalid');
    }

    // Previous contact
    if (!in_array($data['previous_contact'], PREVIOUS_CONTACT_OPTIONS, true)) {
        $errors['previous_contact'] = t('validation.previous_contact');
    }

    // Legal confirmations
    if (($input['authorized_representative'] ?? '') !== '1') {
        $errors['authorized_representative'] = t('validation.authorized');
    }
    if (($input['privacy_consent'] ?? '') !== '1') {
        $errors['privacy_consent'] = t('validation.privacy');
    }

    return [$data, $errors];
}
