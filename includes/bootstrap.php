<?php
/**
 * Shared bootstrap: configuration, error handling and library includes.
 */

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('STORAGE_PATH', APP_ROOT . '/storage');

$GLOBALS['app_config'] = require APP_ROOT . '/config/config.php';

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/captcha.php';
require_once __DIR__ . '/rate_limit.php';
require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/partner_application.php';

date_default_timezone_set((string) config('app.timezone', 'UTC'));

// Errors are logged, never shown to visitors (unless APP_DEBUG=true locally).
error_reporting(E_ALL);
ini_set('display_errors', config('app.debug') ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/php-errors.log');

set_exception_handler(static function (Throwable $e): void {
    log_error('Unhandled exception', ['exception' => $e]);

    if (!headers_sent()) {
        http_response_code(500);
    }

    try {
        $message = (string) t('messages.server_error');
    } catch (Throwable) {
        $message = 'Something went wrong. Please try again later.';
    }

    if (is_ajax_request()) {
        json_response(500, ['ok' => false, 'message' => $message]);
    }

    echo '<!doctype html><meta charset="utf-8"><title>Something went wrong</title>'
        . '<p style="font-family:sans-serif;padding:40px;text-align:center">' . e($message) . '</p>';
    exit;
});
