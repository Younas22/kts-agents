<?php
/**
 * Friendly Captcha (v2) integration.
 *
 * Front end: <div class="frc-captcha" data-sitekey="..."> inside the form; the widget adds a
 * hidden `frc-captcha-response` field. Back end: the response is verified against the
 * siteverify API with the secret API key. Keys come from FRIENDLY_CAPTCHA_SITE_KEY /
 * FRIENDLY_CAPTCHA_SECRET_KEY – when either is missing the CAPTCHA is disabled and a warning is logged.
 */

declare(strict_types=1);

const FRIENDLY_CAPTCHA_SDK_VERSION = '1.1.1';

function captcha_enabled(): bool
{
    return config('captcha.site_key') !== '' && config('captcha.secret_key') !== '';
}

function captcha_verify_url(): string
{
    return config('captcha.endpoint') === 'eu'
        ? 'https://eu.frcapi.com/api/v2/captcha/siteverify'
        : 'https://global.frcapi.com/api/v2/captcha/siteverify';
}

/**
 * Verify a widget response.
 *
 * Returns true when the visitor passed. Invalid, expired or reused responses fail.
 * If the Friendly Captcha API itself cannot be reached or rejects our credentials,
 * the request is allowed (as Friendly Captcha recommends) and the problem is logged,
 * so an outage or misconfiguration never locks real agents out.
 */
function captcha_verify(mixed $response): bool
{
    if (!captcha_enabled()) {
        log_warning('Friendly Captcha is not configured – submission accepted without CAPTCHA check.');
        return true;
    }

    // Empty or sentinel values (".UNSTARTED", ".SOLVING", ".EXPIRED", ...) mean the widget is not done.
    if (!is_string($response) || $response === '' || $response[0] === '.' || strlen($response) > 10000) {
        return false;
    }

    $ch = curl_init(captcha_verify_url());
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . config('captcha.secret_key'),
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'response' => $response,
            'sitekey'  => config('captcha.site_key'),
        ]),
    ]);

    $body   = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error  = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        log_error('Friendly Captcha API unreachable – request allowed', ['curl_error' => $error]);
        return true;
    }

    $json = json_decode((string) $body, true);

    if ($status === 200 && is_array($json)) {
        return ($json['success'] ?? false) === true;
    }

    $code = is_array($json) ? ($json['error']['error_code'] ?? 'unknown') : 'invalid_json';

    // Problems with the visitor's response → fail. Anything else is on our side → allow + log.
    if ($status === 400 && is_string($code) && str_starts_with($code, 'response_')) {
        return false;
    }

    log_error('Friendly Captcha verification error – request allowed', ['status' => $status, 'error_code' => $code]);
    return true;
}
