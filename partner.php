<?php
/**
 * Home page (/) – Khan Travel Services e.K. B2B partner landing page and application endpoint.
 * Served through index.php; old /become-a-partner and *.php URLs redirect here.
 *
 * GET  → landing page
 * POST → process application (JSON for fetch requests, redirect/re-render without JS)
 */

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

send_security_headers();

// The landing page lives at "/". Old URLs (/become-a-partner, /partner.php, /index.php) redirect there.
$requestPath = strtolower(rtrim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/'));
if (in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)
    && (str_ends_with($requestPath, '.php') || str_ends_with($requestPath, '/become-a-partner')
        // live inside Laravel's public/kts: /kts/ itself → the page at the domain root
        || (base_path() !== files_base_path() && $requestPath === strtolower(files_base_path())))) {
    $query = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
    redirect(home_url() . ($query !== '' ? '?' . $query : ''), 301);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['GET', 'HEAD', 'POST'], true)) {
    header('Allow: GET, HEAD, POST');
    http_response_code(405);
    exit;
}

start_secure_session();
header('Cache-Control: no-store, private');

$errors  = [];
$old     = [];
$alert   = null;
$success = false;

if ($method === 'POST') {
    $result = handle_partner_submission($_POST);

    if (is_ajax_request()) {
        json_response($result['status'], $result['payload']);
    }

    // No-JS fallback: Post/Redirect/Get on success, re-render with errors otherwise.
    if ($result['payload']['ok']) {
        $_SESSION['partner_application_submitted'] = true;
        redirect(home_url() . '?lang=' . current_language() . '#apply', 303);
    }

    http_response_code($result['status']);
    $errors = $result['payload']['errors'] ?? [];
    $alert  = $result['payload']['message'] ?? t('messages.server_error');
    $old    = array_map(
        static fn ($v) => is_string($v) ? $v : '',
        array_intersect_key($_POST, array_flip([
            'company_name', 'first_name', 'last_name', 'email', 'phone', 'previous_contact',
            'authorized_representative', 'privacy_consent',
        ]))
    );
} elseif (!empty($_SESSION['partner_application_submitted'])) {
    unset($_SESSION['partner_application_submitted']);
    $success = true;
}

$meta = [
    'title'       => t('meta.partner_title'),
    'description' => t('meta.partner_description'),
    'canonical'   => config('app.url') !== '' ? absolute_url('') : '',
];

require APP_ROOT . '/views/partner-page.php';
