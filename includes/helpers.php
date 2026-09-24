<?php
/**
 * General helpers: config access, escaping, URLs, responses, logging and icons.
 */

declare(strict_types=1);

const MSG_VALIDATION   = 'Please check the highlighted fields and try again.';
const MSG_DUPLICATE    = 'This email address is already registered or has an existing application. Please contact our team if you need assistance.';
const MSG_SERVER_ERROR = 'Something went wrong while submitting your application. Please try again or contact our team.';
const MSG_CSRF         = 'Your session has expired. Please submit the form again.';
const MSG_RATE_LIMIT   = 'Too many attempts from your network. Please wait a few minutes and try again.';

/**
 * Read a config value using dot notation, e.g. config('mail.from').
 */
function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['app_config'] ?? [];
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function is_production(): bool
{
    return config('app.env') === 'production';
}

/** Escape a value for safe HTML output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * URL path prefix of the app (e.g. "" in production or "/kts-agents" locally),
 * derived from the executing script so the app works in any folder.
 */
function base_path(): string
{
    static $base = null;
    if ($base === null) {
        $dir  = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $base = rtrim($dir, '/.');
    }
    return $base;
}

/** Root-relative URL inside the app. */
function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

/** Absolute URL using APP_URL (falls back to the current host). */
function absolute_url(string $path = ''): string
{
    $root = (string) config('app.url');
    if ($root === '') {
        $scheme = is_https() ? 'https' : 'http';
        $host   = preg_replace('/[^A-Za-z0-9.\-:\[\]]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
        $root   = $scheme . '://' . $host . base_path();
    }
    return $root . '/' . ltrim($path, '/');
}

/** Privacy Policy link: PRIVACY_POLICY_URL if set, otherwise this app's /privacy-policy page. */
function privacy_policy_url(): string
{
    $configured = (string) config('app.privacy_policy_url');
    return $configured !== '' ? $configured : url('privacy-policy');
}

/** Versioned asset URL for cache busting. */
function asset(string $path): string
{
    $file    = APP_ROOT . '/assets/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return url('assets/' . ltrim($path, '/')) . '?v=' . $version;
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443
        || strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function is_ajax_request(): bool
{
    return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

/** Send a JSON response and stop. */
function json_response(int $status, array $payload): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function redirect(string $location, int $status = 302): never
{
    header('Location: ' . $location, true, $status);
    exit;
}

function send_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    if (is_https()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
}

function client_ip(): string
{
    // REMOTE_ADDR only: forwarded headers can be spoofed unless a trusted proxy is configured.
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/** "hm.younas22@gmail.com" → "hm***@gmail.com" (keeps personal data out of logs). */
function mask_email(string $email): string
{
    [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
    return mb_substr($local, 0, 2) . '***@' . $domain;
}

// ---------------------------------------------------------------------------
// Logging (storage/logs/app-YYYY-MM-DD.log)
// ---------------------------------------------------------------------------

function log_message(string $level, string $message, array $context = []): void
{
    if (isset($context['exception']) && $context['exception'] instanceof Throwable) {
        $ex = $context['exception'];
        $context['exception'] = sprintf('%s: %s in %s:%d', $ex::class, $ex->getMessage(), $ex->getFile(), $ex->getLine());
    }

    $line = sprintf(
        "[%s] %s: %s %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $message,
        $context ? json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : ''
    );

    $dir = STORAGE_PATH . '/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    if (@file_put_contents($dir . '/app-' . date('Y-m-d') . '.log', $line, FILE_APPEND | LOCK_EX) === false) {
        error_log(trim($line));
    }
}

function log_info(string $message, array $context = []): void
{
    log_message('info', $message, $context);
}

function log_warning(string $message, array $context = []): void
{
    log_message('warning', $message, $context);
}

function log_error(string $message, array $context = []): void
{
    log_message('error', $message, $context);
}

// ---------------------------------------------------------------------------
// Inline SVG icons (24px grid, stroke based – no icon font download)
// ---------------------------------------------------------------------------

function icon(string $name, string $class = 'h-5 w-5'): string
{
    static $paths = [
        'arrow-right'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'arrow-down'   => '<path d="M12 5v14M6 13l6 6 6-6"/>',
        'check'        => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/>',
        'alert-circle' => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5v5.5M12 16.5h.01"/>',
        'globe'        => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
        'user-plus'    => '<circle cx="10" cy="8" r="3.5"/><path d="M3.5 19.5a6.5 6.5 0 0 1 13 0M19 8v6M16 11h6"/>',
        'dashboard'    => '<rect x="3.5" y="3.5" width="7" height="8" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="14.5" width="7" height="6" rx="1.5"/>',
        'ticket'       => '<path d="M3.5 8.5v-2A1.5 1.5 0 0 1 5 5h14a1.5 1.5 0 0 1 1.5 1.5v2a3.5 3.5 0 0 0 0 7v2A1.5 1.5 0 0 1 19 19H5a1.5 1.5 0 0 1-1.5-1.5v-2a3.5 3.5 0 0 0 0-7z"/><path d="M14.5 5.5v2M14.5 11v2M14.5 16.5v2"/>',
        'briefcase'    => '<rect x="3.5" y="7" width="17" height="12.5" rx="2"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7M3.5 12.5h17"/>',
        'headset'      => '<path d="M4.5 14v-2a7.5 7.5 0 0 1 15 0v2"/><rect x="3.5" y="13" width="4" height="6" rx="1.5"/><rect x="16.5" y="13" width="4" height="6" rx="1.5"/><path d="M18.5 19a3 3 0 0 1-3 2.5H13"/>',
        'shield-check' => '<path d="M12 3l7.5 3v5.5c0 4.5-3.2 8.2-7.5 9.5-4.3-1.3-7.5-5-7.5-9.5V6L12 3z"/><path d="M9 12l2 2 4-4"/>',
        'lock'         => '<rect x="5" y="10.5" width="14" height="10" rx="2"/><path d="M8 10.5V8a4 4 0 0 1 8 0v2.5"/>',
        'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 2"/>',
        'mail'         => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6.5l8.5 6.5 8.5-6.5"/>',
        'building'     => '<path d="M4 20.5V5a1.5 1.5 0 0 1 1.5-1.5h8A1.5 1.5 0 0 1 15 5v15.5M15 9.5h3.5A1.5 1.5 0 0 1 20 11v9.5M2.5 20.5h19M7.5 7.5h4M7.5 11h4M7.5 14.5h4"/>',
        'user'         => '<circle cx="12" cy="8" r="3.75"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/>',
        'chevron-down' => '<path d="M6 9l6 6 6-6"/>',
        'plane'        => '<path d="M17.8 19.2L16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>',
        'bed'          => '<path d="M3 18.5V6M3 14.5h18v4M21 14.5V12a3 3 0 0 0-3-3h-7v5.5"/><circle cx="7" cy="11" r="1.75"/>',
        'search'       => '<circle cx="11" cy="11" r="6.5"/><path d="M20 20l-4.3-4.3"/>',
        'wallet'       => '<path d="M19.5 7.5V6A1.5 1.5 0 0 0 18 4.5H5.5a2 2 0 0 0 0 4H20a.5.5 0 0 1 .5.5v3M20.5 16v2a1.5 1.5 0 0 1-1.5 1.5H5.5a2 2 0 0 1-2-2v-11"/><path d="M21 12h-3.5a2 2 0 0 0 0 4H21z"/>',
        'home'         => '<path d="M4 10.5L12 4l8 6.5V19a1.5 1.5 0 0 1-1.5 1.5H15V15h-6v5.5H5.5A1.5 1.5 0 0 1 4 19z"/>',
        'file'         => '<path d="M14 3.5H7A1.5 1.5 0 0 0 5.5 5v14A1.5 1.5 0 0 0 7 20.5h10a1.5 1.5 0 0 0 1.5-1.5V8z"/><path d="M14 3.5V8h4.5M9 12.5h6M9 16h6"/>',
        'users'        => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 19.5a6.5 6.5 0 0 1 13 0M16 4.8a3.5 3.5 0 0 1 0 6.4M18 14a6.5 6.5 0 0 1 3.5 5.5"/>',
        'bell'         => '<path d="M6 16.5V11a6 6 0 0 1 12 0v5.5l1.5 2h-15zM10 20.5a2 2 0 0 0 4 0"/>',
    ];

    $body = $paths[$name] ?? '';

    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" '
        . 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $body . '</svg>';
}
