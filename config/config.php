<?php
/**
 * Application configuration.
 *
 * Values come from real environment variables first, then from the `.env`
 * file in the project root. No secrets are stored in this file.
 */

declare(strict_types=1);

/**
 * Parse a simple KEY=VALUE .env file (comments, blank lines and quoted values supported).
 *
 * @return array<string, string>
 */
function load_env_file(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        return [];
    }

    $values = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if (!preg_match('/^[A-Z][A-Z0-9_]*$/', $key)) {
            continue;
        }

        $quoted = strlen($value) >= 2
            && ($value[0] === '"' || $value[0] === "'")
            && substr($value, -1) === $value[0];

        if ($quoted) {
            $value = substr($value, 1, -1);
        } elseif (($hash = strpos($value, ' #')) !== false) {
            $value = rtrim(substr($value, 0, $hash));
        }

        $values[$key] = $value;
    }

    return $values;
}

function env(string $key, ?string $default = null): ?string
{
    static $file = null;
    $file ??= load_env_file(dirname(__DIR__) . '/.env');

    $value = getenv($key);
    if ($value === false) {
        $value = $_SERVER[$key] ?? $file[$key] ?? null;
    }

    return ($value === null || $value === '') ? $default : (string) $value;
}

function env_bool(string $key, bool $default = false): bool
{
    $value = env($key);
    return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

$appUrl      = rtrim((string) env('APP_URL', ''), '/');
$mainSiteUrl = rtrim((string) env('MAIN_SITE_URL', ''), '/');

return [
    'app' => [
        'name'               => 'Khan Travel Services',
        'platform'           => 'Khan Travel Services',
        'env'                => env('APP_ENV', 'production'),
        'debug'              => env_bool('APP_DEBUG'),
        'url'                => $appUrl,
        'timezone'           => env('APP_TIMEZONE', 'UTC'),
        'main_site_url'      => $mainSiteUrl,
        // Empty → this app's own /privacy-policy page.
        'privacy_policy_url' => env('PRIVACY_POLICY_URL', ''),
        'contact_email'      => env('CONTACT_EMAIL', ''),
        // Password for /language-settings. Empty = settings page disabled.
        'language_admin_password' => env('LANGUAGE_ADMIN_PASSWORD', ''),
    ],

    'db' => [
        'host'     => env('DB_HOST', '127.0.0.1'),
        'port'     => (int) env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'khantravel'),
        'username' => env('DB_USERNAME', ''),
        'password' => env('DB_PASSWORD', ''),
    ],

    'mail' => [
        'resend_api_key'   => env('RESEND_API_KEY', ''),
        'resend_api_url'   => rtrim((string) env('RESEND_API_URL', 'https://api.resend.com'), '/'),
        'from'             => env('MAIL_FROM') ?? env('MAIL_FROM_ADDRESS', ''),
        'from_name'        => env('MAIL_FROM_NAME', 'Khan Travel Services'),
        'reply_to'         => env('MAIL_REPLY_TO', ''),
        'admin_email'      => env('ADMIN_EMAIL', ''),
        // Empty → the logo is embedded in each email as an inline (CID) image.
        'logo_url'         => env('LOGO_URL', ''),
        'admin_review_url' => env('ADMIN_REVIEW_URL', ''),
        'preview'          => env_bool('MAIL_PREVIEW'),
    ],

    'captcha' => [
        'site_key'   => env('FRIENDLY_CAPTCHA_SITE_KEY', ''),
        'secret_key' => env('FRIENDLY_CAPTCHA_SECRET_KEY', ''),
        'endpoint'   => env('FRIENDLY_CAPTCHA_ENDPOINT', 'global') === 'eu' ? 'eu' : 'global',
        // Show a "not configured" notice in the form (local only) while keys are missing.
        'show_placeholder' => env_bool('FRIENDLY_CAPTCHA_SHOW_PLACEHOLDER'),
    ],

    'rate_limit' => [
        'max_attempts' => max(1, (int) env('RATE_LIMIT_MAX_ATTEMPTS', '8')),
        'window'       => max(60, (int) env('RATE_LIMIT_WINDOW', '900')),
    ],
];
