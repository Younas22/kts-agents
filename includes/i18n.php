<?php
/**
 * Translations (JSON files, no database).
 *
 *   lang/languages.json  → default language + which languages are active
 *   lang/{code}.json     → one translation file per language
 *
 * Language choice: ?lang=xx (also a hidden form field) → kt_lang cookie → default language.
 */

declare(strict_types=1);

const LANG_PATH        = APP_ROOT . '/lang';
const LANG_COOKIE      = 'kt_lang';
const LANG_CODE_REGEX  = '/^[a-z]{2}(-[a-z]{2})?$/';

/**
 * @return array{default: string, languages: array<string, array{name: string, native: string, active: bool}>}
 */
function languages_config(bool $fresh = false): array
{
    static $config = null;
    if ($config !== null && !$fresh) {
        return $config;
    }

    $fallback = ['default' => 'en', 'languages' => ['en' => ['name' => 'English', 'native' => 'English', 'active' => true]]];
    $raw      = @file_get_contents(LANG_PATH . '/languages.json');
    $json     = is_string($raw) ? json_decode($raw, true) : null;

    if (!is_array($json) || !is_array($json['languages'] ?? null)) {
        log_error('lang/languages.json is missing or invalid – using English only');
        return $config = $fallback;
    }

    $languages = [];
    foreach ($json['languages'] as $code => $info) {
        if (!is_string($code) || !preg_match(LANG_CODE_REGEX, $code) || !is_array($info)) {
            continue;
        }
        $languages[$code] = [
            'name'   => (string) ($info['name'] ?? $code),
            'native' => (string) ($info['native'] ?? $info['name'] ?? $code),
            'active' => (bool) ($info['active'] ?? false),
        ];
    }

    if (!$languages) {
        return $config = $fallback;
    }

    return $config = ['default' => (string) ($json['default'] ?? 'en'), 'languages' => $languages];
}

/** @return array<string, array{name: string, native: string, active: bool}> */
function active_languages(): array
{
    $active = array_filter(languages_config()['languages'], static fn ($l) => $l['active']);
    return $active ?: array_slice(languages_config()['languages'], 0, 1, true);
}

function default_language(): string
{
    $default = languages_config()['default'];
    return isset(active_languages()[$default]) ? $default : (string) array_key_first(active_languages());
}

function is_active_language(mixed $code): bool
{
    return is_string($code) && isset(active_languages()[$code]);
}

/**
 * Current language for this request (resolved once).
 */
function current_language(?string $set = null): string
{
    static $current = null;

    if ($set !== null) {
        return $current = $set;
    }
    if ($current !== null) {
        return $current;
    }

    $requested = $_GET['lang'] ?? $_POST['lang'] ?? null;
    if (is_active_language($requested)) {
        remember_language($requested);
        return $current = $requested;
    }

    $cookie = $_COOKIE[LANG_COOKIE] ?? null;
    if (is_active_language($cookie)) {
        return $current = $cookie;
    }

    return $current = default_language();
}

function remember_language(string $code): void
{
    if (headers_sent() || ($_COOKIE[LANG_COOKIE] ?? null) === $code) {
        return;
    }
    setcookie(LANG_COOKIE, $code, [
        'expires'  => time() + 365 * 24 * 3600,
        'path'     => base_path() === '' ? '/' : base_path() . '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/** Translation data for one language (cached). */
function translations(string $code): array
{
    static $cache = [];
    if (!isset($cache[$code])) {
        $raw  = preg_match(LANG_CODE_REGEX, $code) ? @file_get_contents(LANG_PATH . "/{$code}.json") : false;
        $data = is_string($raw) ? json_decode($raw, true) : null;
        if (!is_array($data)) {
            if ($raw !== false) {
                log_error("Translation file lang/{$code}.json contains invalid JSON");
            }
            $data = [];
        }
        $cache[$code] = $data;
    }
    return $cache[$code];
}

function translation_lookup(array $data, string $key): mixed
{
    foreach (explode('.', $key) as $segment) {
        if (!is_array($data) || !array_key_exists($segment, $data)) {
            return null;
        }
        $data = $data[$segment];
    }
    return $data;
}

/**
 * Translate a dot-notation key. Falls back to the default language, then English, then the key itself.
 * Strings get ":name" placeholders replaced; arrays (lists) are returned as-is.
 */
function t(string $key, array $replace = [], ?string $lang = null): mixed
{
    $lang ??= current_language();

    foreach (array_unique([$lang, default_language(), 'en']) as $code) {
        $value = translation_lookup(translations($code), $key);
        if ($value !== null) {
            break;
        }
    }

    if ($value === null) {
        return $key;
    }
    if (is_string($value) && $replace) {
        $value = strtr($value, array_combine(
            array_map(static fn ($k) => ':' . $k, array_keys($replace)),
            array_map('strval', array_values($replace))
        ));
    }
    return $value;
}

/** Translated, HTML-escaped string. */
function te(string $key, array $replace = []): string
{
    return e(t($key, $replace));
}

/** Translated list (array) – empty array when missing. */
function tl(string $key): array
{
    $value = t($key);
    return is_array($value) ? $value : [];
}

/**
 * Escaped translation with raw-HTML placeholders, e.g.
 * th('form.privacy_consent', ['link' => '<a …>Privacy Policy</a>'])
 */
function th(string $key, array $html): string
{
    $escaped = e(t($key));
    foreach ($html as $name => $markup) {
        $escaped = str_replace(':' . $name, $markup, $escaped);
    }
    return $escaped;
}

/** Run $fn with another language active (used for emails), then restore. */
function with_language(string $code, callable $fn): mixed
{
    $previous = current_language();
    current_language($code);
    try {
        return $fn();
    } finally {
        current_language($previous);
    }
}

/** Current page URL with ?lang=$code (other query parameters kept). */
function language_url(string $code): string
{
    $path  = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $query = [];
    parse_str((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY), $query);
    $query['lang'] = $code;
    return $path . '?' . http_build_query($query);
}
