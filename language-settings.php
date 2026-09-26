<?php
/**
 * /language-settings – choose the default language and switch languages on/off.
 *
 * Stores everything in lang/languages.json (no database).
 * Protected by LANGUAGE_ADMIN_PASSWORD from .env; disabled when that is empty.
 */

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

send_security_headers();
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store, private');

$requestPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
if (str_ends_with(strtolower($requestPath), '.php')) {
    redirect(url('language-settings'), 301);
}

start_secure_session();

$selfUrl  = url('language-settings');
$password = (string) config('app.language_admin_password');
$enabled  = $password !== '';
$isAdmin  = $enabled && ($_SESSION['lang_admin'] ?? false) === true;

/** Leaf keys of a translation array in dot notation (lists count as one key). */
function translation_keys(array $data, string $prefix = ''): array
{
    $keys = [];
    foreach ($data as $key => $value) {
        $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
        if (is_array($value) && !array_is_list($value)) {
            $keys = array_merge($keys, translation_keys($value, $path));
        } else {
            $keys[] = $path;
        }
    }
    return $keys;
}

function flash(string $type, string $message): void
{
    $_SESSION['lang_flash'] = ['type' => $type, 'message' => $message];
}

// ---------------------------------------------------------------------------
// Actions
// ---------------------------------------------------------------------------
if ($enabled && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if (!csrf_verify($_POST['_token'] ?? null)) {
        flash('error', 'Your session expired. Please try again.');
        redirect($selfUrl, 303);
    }

    if ($action === 'login') {
        if (!rate_limit_hit('lang-admin|' . client_ip(), 5, 900)) {
            flash('error', 'Too many login attempts. Please wait 15 minutes.');
        } elseif (hash_equals(hash('sha256', $password), hash('sha256', (string) ($_POST['password'] ?? '')))) {
            session_regenerate_id(true);
            $_SESSION['lang_admin'] = true;
            log_info('Language settings: admin logged in', ['ip' => client_ip()]);
        } else {
            log_warning('Language settings: wrong password', ['ip' => client_ip()]);
            flash('error', 'Wrong password.');
        }
        redirect($selfUrl, 303);
    }

    if ($action === 'logout') {
        unset($_SESSION['lang_admin']);
        session_regenerate_id(true);
        redirect($selfUrl, 303);
    }

    if ($action === 'save' && $isAdmin) {
        $config  = languages_config(true);
        $codes   = array_keys($config['languages']);
        $active  = array_values(array_intersect($codes, array_map('strval', (array) ($_POST['active'] ?? []))));
        $default = (string) ($_POST['default'] ?? '');

        $problem = null;
        if (!$active) {
            $problem = 'At least one language must be active.';
        } elseif (!in_array($default, $active, true)) {
            $problem = 'The default language must also be active.';
        } else {
            foreach ($active as $code) {
                if (!translations($code)) {
                    $problem = "lang/{$code}.json is missing or not valid JSON – fix it before activating " . strtoupper($code) . '.';
                    break;
                }
            }
        }

        if ($problem === null) {
            $new = ['default' => $default, 'languages' => []];
            foreach ($config['languages'] as $code => $info) {
                $new['languages'][$code] = ['name' => $info['name'], 'native' => $info['native'], 'active' => in_array($code, $active, true)];
            }

            $file = LANG_PATH . '/languages.json';
            $tmp  = $file . '.tmp';
            $json = json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

            if (@file_put_contents($tmp, $json, LOCK_EX) === false || !@rename($tmp, $file)) {
                @unlink($tmp);
                log_error('Language settings: could not write lang/languages.json');
                $problem = 'Could not save: the web server cannot write to lang/languages.json. Check the file permissions.';
            } else {
                log_info('Language settings saved', ['default' => $default, 'active' => $active, 'ip' => client_ip()]);
                flash('success', 'Settings saved. Default language: ' . $new['languages'][$default]['name'] . '.');
            }
        }

        if ($problem !== null) {
            flash('error', $problem);
        }
        redirect($selfUrl, 303);
    }

    redirect($selfUrl, 303);
}

// ---------------------------------------------------------------------------
// View data
// ---------------------------------------------------------------------------
$flash = $_SESSION['lang_flash'] ?? null;
unset($_SESSION['lang_flash']);

$config    = languages_config(true);
$reference = translation_keys(translations('en'));
$status    = [];
foreach ($config['languages'] as $code => $info) {
    $data          = translations($code);
    $keys          = translation_keys($data);
    $status[$code] = [
        'file'    => "lang/{$code}.json",
        'valid'   => (bool) $data,
        'keys'    => count($keys),
        'missing' => array_values(array_diff($reference, $keys)),
    ];
}

$meta = ['title' => 'Language Settings | Khan Travel Services e.K.', 'description' => 'Language settings', 'noindex' => true];
require APP_ROOT . '/views/partials/head.php';
?>
<body class="min-h-screen bg-surface">
<header class="border-b border-line bg-white">
    <div class="mx-auto flex h-16 max-w-4xl items-center justify-between gap-4 px-4 sm:px-6">
        <a href="<?= e(url('become-a-partner')) ?>" class="flex items-center gap-3">
            <img src="<?= e(asset('images/khan-travel-logo.png')) ?>" alt="Khan Travel Services e.K." width="128" height="128" class="h-10 w-10">
            <span class="leading-tight"><span class="block font-bold text-ink">Khan Travel Services e.K.</span><span class="block text-xs text-ink-muted">Language settings</span></span>
        </a>
        <?php if ($isAdmin): ?>
            <form method="post" action="<?= e($selfUrl) ?>">
                <?= csrf_field() ?><input type="hidden" name="action" value="logout">
                <button class="btn-secondary h-10 min-h-0 px-4 text-sm">Log out</button>
            </form>
        <?php endif; ?>
    </div>
</header>

<main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 sm:py-14">
    <h1 class="text-2xl font-bold tracking-[-0.02em] text-ink sm:text-3xl">Language settings</h1>
    <p class="mt-2 text-[15px] text-ink-muted">Choose the default language and which languages visitors can pick. Saved in <code class="rounded bg-white px-1.5 py-0.5 text-[13px] ring-1 ring-line">lang/languages.json</code>.</p>

    <?php if ($flash): ?>
        <div class="mt-6 flex items-start gap-3 rounded-lg border p-4 text-sm <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' ?>" role="<?= $flash['type'] === 'success' ? 'status' : 'alert' ?>">
            <?= icon($flash['type'] === 'success' ? 'check-circle' : 'alert-circle', 'mt-px h-5 w-5 flex-none') ?>
            <p><?= e($flash['message']) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!$enabled): ?>
        <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-6 text-[15px] leading-relaxed text-amber-900">
            <p class="font-semibold">This page is disabled.</p>
            <p class="mt-1">Set <code class="rounded bg-amber-100 px-1">LANGUAGE_ADMIN_PASSWORD</code> in <code class="rounded bg-amber-100 px-1">.env</code> to enable it.</p>
        </div>

    <?php elseif (!$isAdmin): ?>
        <form method="post" action="<?= e($selfUrl) ?>" class="mt-8 max-w-sm rounded-2xl border border-line bg-white p-6 shadow-card sm:p-8">
            <?= csrf_field() ?><input type="hidden" name="action" value="login">
            <label for="password" class="field-label">Password</label>
            <input id="password" name="password" type="password" class="field-control" autocomplete="current-password" required autofocus>
            <button class="btn-primary mt-5 h-12 w-full">Log in</button>
        </form>

    <?php else: ?>
        <form method="post" action="<?= e($selfUrl) ?>" class="mt-8">
            <?= csrf_field() ?><input type="hidden" name="action" value="save">

            <div class="overflow-x-auto rounded-2xl border border-line bg-white shadow-card">
                <table class="w-full min-w-[40rem] text-left text-[15px]">
                    <thead class="border-b border-line bg-surface text-[13px] font-semibold uppercase tracking-[0.05em] text-ink-muted">
                        <tr>
                            <th scope="col" class="px-5 py-3">Language</th>
                            <th scope="col" class="px-5 py-3">Translation file</th>
                            <th scope="col" class="px-5 py-3 text-center">Active</th>
                            <th scope="col" class="px-5 py-3 text-center">Default</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <?php foreach ($config['languages'] as $code => $info): $s = $status[$code]; ?>
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-ink"><?= e($info['name']) ?> <span class="ml-1 rounded bg-surface px-1.5 py-0.5 text-xs font-semibold uppercase text-ink-muted ring-1 ring-line"><?= e($code) ?></span></p>
                                    <p class="text-sm text-ink-muted"><?= e($info['native']) ?></p>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    <p class="font-mono text-[13px] text-ink-soft"><?= e($s['file']) ?></p>
                                    <?php if (!$s['valid']): ?>
                                        <p class="mt-1 font-semibold text-red-700">Missing or invalid JSON</p>
                                    <?php elseif ($s['missing']): ?>
                                        <p class="mt-1 font-semibold text-amber-700"><?= count($s['missing']) ?> text(s) missing – English is shown instead</p>
                                        <details class="mt-1 text-xs text-ink-muted"><summary class="cursor-pointer">Show missing keys</summary><p class="mt-1 break-all font-mono"><?= e(implode(', ', $s['missing'])) ?></p></details>
                                    <?php else: ?>
                                        <p class="mt-1 font-semibold text-emerald-700">Complete (<?= $s['keys'] ?> texts)</p>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <input type="checkbox" name="active[]" value="<?= e($code) ?>" class="check-input" aria-label="Active: <?= e($info['name']) ?>" <?= $info['active'] ? 'checked' : '' ?>>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <input type="radio" name="default" value="<?= e($code) ?>" class="h-5 w-5 cursor-pointer accent-brand-500" aria-label="Default: <?= e($info['name']) ?>" <?= $config['default'] === $code ? 'checked' : '' ?>>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-ink-muted">Visitors can switch between active languages with the dropdown in the header. The default language is shown to first-time visitors.</p>
                <button class="btn-primary h-12 flex-none px-6">Save settings</button>
            </div>
        </form>

        <div class="mt-10 rounded-2xl border border-line bg-white p-6 text-sm leading-relaxed text-ink-muted">
            <p class="font-semibold text-ink">Add another language</p>
            <ol class="mt-2 list-decimal space-y-1 pl-5">
                <li>Copy <code class="rounded bg-surface px-1 ring-1 ring-line">lang/en.json</code> to e.g. <code class="rounded bg-surface px-1 ring-1 ring-line">lang/fr.json</code> and translate the texts.</li>
                <li>Add it to <code class="rounded bg-surface px-1 ring-1 ring-line">lang/languages.json</code>: <code class="rounded bg-surface px-1 ring-1 ring-line">"fr": { "name": "French", "native": "Français", "active": false }</code></li>
                <li>Come back here and activate it.</li>
            </ol>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
