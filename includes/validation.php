<?php
/**
 * Server-side validation for the partner application form.
 */

declare(strict_types=1);

/** Allowed "previous contact" values: stored key => label shown to people. */
const PREVIOUS_CONTACT_OPTIONS = [
    'none'                 => "I haven't had any contact",
    'sales_team'           => 'Sales Team',
    'business_development' => 'Business Development',
    'support_team'         => 'Support Team',
    'other'                => 'Other',
];

function previous_contact_label(?string $key): string
{
    return PREVIOUS_CONTACT_OPTIONS[$key ?? ''] ?? 'Not specified';
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
        $errors['company_name'] = 'Please enter your company name.';
    } elseif ($len < 2 || $len > 255) {
        $errors['company_name'] = 'Company name must be between 2 and 255 characters.';
    } elseif (preg_match('/[<>{}]/', $data['company_name'])) {
        $errors['company_name'] = 'Company name contains characters that are not allowed.';
    }

    // First / last name
    foreach (['first_name' => 'first name', 'last_name' => 'last name'] as $field => $label) {
        $len = mb_strlen($data[$field]);
        if ($len === 0) {
            $errors[$field] = "Please enter your {$label}.";
        } elseif ($len > 100) {
            $errors[$field] = ucfirst($label) . ' must be 100 characters or fewer.';
        } elseif (!preg_match($namePattern, $data[$field])) {
            $errors[$field] = 'Please use letters only (spaces, hyphens and apostrophes are fine).';
        }
    }

    // Email
    if ($data['email'] === '') {
        $errors['email'] = 'Please enter your business email address.';
    } elseif (mb_strlen($data['email']) > 254 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address, e.g. name@youragency.com.';
    }

    // Phone: digits with optional +, spaces, dashes, dots and brackets; 7–15 digits (E.164 max).
    $digits = preg_replace('/\D+/', '', $data['phone']) ?? '';
    if ($data['phone'] === '') {
        $errors['phone'] = 'Please enter your phone number.';
    } elseif (mb_strlen($data['phone']) > 30 || !preg_match('/^\+?[0-9\s().\-]+$/', $data['phone'])
        || strlen($digits) < 7 || strlen($digits) > 15) {
        $errors['phone'] = 'Please enter a valid phone number, e.g. +92 300 1234567.';
    }

    // Previous contact
    if (!array_key_exists($data['previous_contact'], PREVIOUS_CONTACT_OPTIONS)) {
        $errors['previous_contact'] = 'Please choose an option from the list.';
    }

    // Legal confirmations
    if (($input['authorized_representative'] ?? '') !== '1') {
        $errors['authorized_representative'] = 'Please confirm that you are authorized to represent this company.';
    }
    if (($input['privacy_consent'] ?? '') !== '1') {
        $errors['privacy_consent'] = 'Please accept the Privacy Policy to continue.';
    }

    return [$data, $errors];
}
