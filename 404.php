<?php
/**
 * Branded "Page Not Found" page (used by .htaccess for unknown URLs and ErrorDocument 404).
 */

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

http_response_code(404);
send_security_headers();

$mainSite    = (string) config('app.main_site_url');
$homeUrl     = $mainSite !== '' ? $mainSite : url('become-a-partner');
$sectionBase = url('become-a-partner');

$meta = [
    'title'       => 'Page Not Found | Khan Travel',
    'description' => "The page you're looking for doesn't exist or may have moved.",
    'noindex'     => true,
];

require APP_ROOT . '/views/partials/head.php';
?>
<body class="flex min-h-screen flex-col">
<?php require APP_ROOT . '/views/partials/site-header.php'; ?>

<main id="main" class="relative flex flex-1 items-center overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-dots opacity-40 [mask-image:radial-gradient(ellipse_60%_60%_at_50%_40%,#000_10%,transparent_70%)]" aria-hidden="true"></div>

    <div class="relative mx-auto w-full max-w-xl px-4 py-20 text-center sm:px-6 sm:py-28">
        <p class="fade-up text-[5.5rem] font-bold leading-none tracking-[-0.05em] text-brand-500 sm:text-[7.5rem]" aria-hidden="true">404</p>
        <h1 class="fade-up fade-up-delay mt-4 text-2xl font-bold tracking-[-0.02em] text-ink sm:text-[2rem]">
            <span class="sr-only">Error 404: </span>Page Not Found
        </h1>
        <p class="fade-up fade-up-delay mx-auto mt-4 max-w-md text-pretty text-base leading-relaxed text-ink-muted sm:text-[17px]">
            The page you're looking for doesn't exist or may have moved.
        </p>

        <div class="fade-up fade-up-delay mt-9 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="<?= e($homeUrl) ?>" class="btn-primary h-12 px-6 text-base">Back to Khan Travel</a>
            <a href="<?= e(url('become-a-partner')) ?>" class="btn-secondary h-12 px-6 text-base">Become a Partner</a>
        </div>
    </div>
</main>

<?php require APP_ROOT . '/views/partials/site-footer.php'; ?>
</body>
</html>
