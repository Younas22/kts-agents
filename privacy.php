<?php
/**
 * /privacy-policy – privacy notice for the partner registration page.
 */

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

send_security_headers();

// Keep ".php" out of public URLs: /privacy.php → /privacy-policy
$requestPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if (str_ends_with(strtolower($requestPath), '.php')) {
    redirect(url('privacy-policy'), 301);
}

$meta = [
    'title'       => 'Privacy Policy | Khan Travel',
    'description' => 'How Khan Travel handles personal data submitted through the B2B partner application form.',
    'canonical'   => config('app.url') !== '' ? absolute_url('privacy-policy') : '',
];

require APP_ROOT . '/views/privacy-page.php';
