<?php
/**
 * Document head.
 *
 * @var array{title: string, description: string, canonical?: string, noindex?: bool} $meta
 */
?>
<!doctype html>
<html lang="en" class="scroll-pt-24">
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
<?php if (!empty($meta['canonical'])): ?>
    <link rel="canonical" href="<?= e($meta['canonical']) ?>">
    <meta property="og:url" content="<?= e($meta['canonical']) ?>">
<?php endif; ?>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Khan Travel">
    <meta property="og:title" content="<?= e($meta['title']) ?>">
    <meta property="og:description" content="<?= e($meta['description']) ?>">
    <link rel="preload" href="<?= e(url('assets/fonts/inter-latin-var.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
