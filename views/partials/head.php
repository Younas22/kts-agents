<?php
/**
 * Document head.
 *
 * @var array{title: string, description: string, canonical?: string, noindex?: bool} $meta
 */

// Language versions: default language at the plain URL, others at ?lang=xx.
$canonicalBase = (string) ($meta['canonical'] ?? '');
$langUrl       = static fn (string $code): string => $code === default_language() ? $canonicalBase : $canonicalBase . '?lang=' . $code;
?>
<!doctype html>
<html lang="<?= e(current_language()) ?>" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($meta['title']) ?></title>
    <meta name="description" content="<?= e($meta['description']) ?>">
    <meta name="theme-color" content="#0077BE">
    <meta name="format-detection" content="telephone=no">
<?php if (!empty($meta['noindex'])): ?>
    <meta name="robots" content="noindex, follow">
<?php endif; ?>
<?php if ($canonicalBase !== ''): ?>
    <link rel="canonical" href="<?= e($langUrl(current_language())) ?>">
    <meta property="og:url" content="<?= e($langUrl(current_language())) ?>">
<?php if (count(active_languages()) > 1): foreach (array_keys(active_languages()) as $code): ?>
    <link rel="alternate" hreflang="<?= e($code) ?>" href="<?= e($langUrl($code)) ?>">
<?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= e($canonicalBase) ?>">
<?php endif; ?>
<?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Khan Travel Services e.K.">
    <meta property="og:locale" content="<?= e(current_language()) ?>">
    <meta property="og:title" content="<?= e($meta['title']) ?>">
    <meta property="og:description" content="<?= e($meta['description']) ?>">
    <link rel="icon" href="<?= e(url('favicon.ico')) ?>" sizes="48x48">
    <link rel="icon" type="image/png" href="<?= e(asset('images/favicon-32.png')) ?>" sizes="32x32">
    <link rel="apple-touch-icon" href="<?= e(asset('images/apple-touch-icon.png')) ?>">
    <link rel="preload" href="<?= e(url('assets/fonts/inter-latin-var.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
