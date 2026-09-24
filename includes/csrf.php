<?php
/**
 * Secure session start and CSRF token handling.
 */

declare(strict_types=1);

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_name('kt_partner');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => base_path() === '' ? '/' : base_path() . '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/**
 * Validate the submitted token and, when present, that the request came from this site.
 */
function csrf_verify(mixed $token): bool
{
    $expected = $_SESSION['csrf_token'] ?? '';

    if (!is_string($token) || !is_string($expected) || $expected === '' || !hash_equals($expected, $token)) {
        return false;
    }

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin !== '' && $origin !== 'null') {
        $originHost = parse_url($origin, PHP_URL_HOST);
        $serverHost = parse_url('//' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST);
        if (!$originHost || strcasecmp($originHost, (string) $serverHost) !== 0) {
            return false;
        }
    }

    return true;
}
