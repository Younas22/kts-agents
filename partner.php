<?php
/**
 * /become-a-partner – Khan Travel Services e.K. B2B partner landing page and application endpoint.
 *
 * GET  → landing page
 * POST → process application (JSON for fetch requests, redirect/re-render without JS)
 */

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

send_security_headers();

// Keep ".php" out of public URLs: /partner.php → /become-a-partner
$requestPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if (str_ends_with(strtolower($requestPath), '.php')) {
    $query = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
    redirect(url('become-a-partner') . ($query !== '' ? '?' . $query : ''), 301);
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
        redirect(url('become-a-partner') . '?lang=' . current_language() . '#apply', 303);
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
    'canonical'   => config('app.url') !== '' ? absolute_url('become-a-partner') : '',
];

require APP_ROOT . '/views/partner-page.php';
