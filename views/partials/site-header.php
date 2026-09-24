<?php
/**
 * Sticky site header.
 *
 * @var string $sectionBase '' on the landing page, or the landing page URL on other pages
 */
$sectionBase ??= '';
?>
<a href="#main" class="sr-only z-50 rounded-md bg-white px-4 py-2 font-semibold text-brand-700 shadow-lift focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to content</a>

<header class="sticky top-0 z-40 border-b border-line/80 bg-white/90 backdrop-blur-md" style="padding-top: env(safe-area-inset-top, 0px)">
    <div class="mx-auto flex h-16 max-w-page items-center justify-between gap-4 px-4 sm:px-6 lg:h-[72px] lg:px-8">
        <a href="<?= e(url('become-a-partner')) ?>" class="flex min-w-0 items-center gap-2.5 rounded-md sm:gap-3">
            <img src="<?= e(asset('images/khan-travel-logo.png')) ?>" alt="Khan Travel" width="128" height="128" class="h-10 w-10 flex-none sm:h-12 sm:w-12">
            <span class="min-w-0 leading-tight">
                <span class="block truncate text-[15px] font-bold tracking-[-0.01em] text-ink sm:text-base">Khan Travel</span>
                <span class="hidden truncate text-xs font-medium text-ink-muted min-[400px]:block">B2B Partner Program</span>
            </span>
        </a>

        <nav aria-label="Primary" class="hidden lg:block">
            <ul class="flex items-center gap-1 text-[15px] font-medium text-ink-soft">
                <li><a href="<?= e($sectionBase) ?>#benefits" class="rounded-md px-3 py-2 transition-colors hover:bg-surface hover:text-ink">Benefits</a></li>
                <li><a href="<?= e($sectionBase) ?>#how-it-works" class="rounded-md px-3 py-2 transition-colors hover:bg-surface hover:text-ink">How It Works</a></li>
                <li><a href="<?= e($sectionBase) ?>#apply" class="rounded-md px-3 py-2 transition-colors hover:bg-surface hover:text-ink">Apply</a></li>
            </ul>
        </nav>

        <a href="<?= e($sectionBase) ?>#apply" class="btn-primary h-10 min-h-0 flex-none px-4 text-sm sm:h-11 sm:px-5 sm:text-[15px]">Become a Partner</a>
    </div>
</header>
