<?php
/**
 * Sticky site header with language switcher.
 *
 * @var string $sectionBase '' on the landing page, or the landing page URL on other pages
 */
$sectionBase ??= '';
$languages   = active_languages();
$currentLang = current_language();
?>
<a href="#main" class="sr-only z-50 rounded-md bg-white px-4 py-2 font-semibold text-brand-700 shadow-lift focus:not-sr-only focus:fixed focus:left-4 focus:top-4"><?= te('site.skip') ?></a>

<header class="sticky top-0 z-40 border-b border-line/80 bg-white/90 backdrop-blur-md" style="padding-top: env(safe-area-inset-top, 0px)">
    <div class="mx-auto flex h-16 max-w-page items-center justify-between gap-3 px-4 sm:gap-4 sm:px-6 lg:h-[72px] lg:px-8">
        <a href="<?= e(url('become-a-partner')) ?>" class="flex min-w-0 items-center gap-2.5 rounded-md sm:gap-3">
            <img src="<?= e(asset('images/khan-travel-logo.png')) ?>" alt="<?= te('site.logo_alt') ?>" width="128" height="128" class="h-10 w-10 flex-none sm:h-12 sm:w-12">
            <span class="min-w-0 leading-tight">
                <span class="block truncate text-[15px] font-bold tracking-[-0.01em] text-ink sm:text-base"><?= te('site.brand') ?></span>
                <span class="hidden truncate text-xs font-medium text-ink-muted sm:block"><?= te('site.program') ?></span>
            </span>
        </a>

        <nav aria-label="<?= te('nav.primary') ?>" class="hidden lg:block">
            <ul class="flex items-center gap-1 text-[15px] font-medium text-ink-soft">
                <li><a href="<?= e($sectionBase) ?>#benefits" class="rounded-md px-3 py-2 transition-colors hover:bg-surface hover:text-ink"><?= te('nav.benefits') ?></a></li>
                <li><a href="<?= e($sectionBase) ?>#how-it-works" class="rounded-md px-3 py-2 transition-colors hover:bg-surface hover:text-ink"><?= te('nav.how') ?></a></li>
                <li><a href="<?= e($sectionBase) ?>#apply" class="rounded-md px-3 py-2 transition-colors hover:bg-surface hover:text-ink"><?= te('nav.apply') ?></a></li>
            </ul>
        </nav>

        <div class="flex flex-none items-center gap-2 sm:gap-3">
            <?php if (count($languages) > 1): ?>
                <details class="relative" data-lang-menu>
                    <summary class="flex h-10 cursor-pointer list-none items-center gap-1.5 rounded-lg border border-line bg-white px-2.5 text-sm font-semibold text-ink-soft transition-colors hover:border-brand-200 hover:text-ink sm:h-11 sm:px-3 [&::-webkit-details-marker]:hidden"
                             aria-label="<?= te('nav.choose_language') ?>: <?= e($languages[$currentLang]['native'] ?? $currentLang) ?>">
                        <?= icon('globe', 'hidden h-[18px] w-[18px] text-brand-500 min-[400px]:block') ?>
                        <span class="uppercase"><?= e($currentLang) ?></span>
                        <?= icon('chevron-down', 'h-4 w-4 text-ink-muted') ?>
                    </summary>
                    <ul class="absolute right-0 z-50 mt-2 min-w-[11rem] rounded-xl border border-line bg-white p-1.5 shadow-lift">
                        <?php foreach ($languages as $code => $info): $isCurrent = $code === $currentLang; ?>
                            <li>
                                <a href="<?= e(language_url($code)) ?>" lang="<?= e($code) ?>" hreflang="<?= e($code) ?>"
                                   class="flex items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors <?= $isCurrent ? 'bg-brand-50 font-semibold text-brand-700' : 'text-ink-soft hover:bg-surface hover:text-ink' ?>"
                                   <?= $isCurrent ? 'aria-current="true"' : '' ?>>
                                    <span><?= e($info['native']) ?> <span class="ml-1 text-xs uppercase text-ink-muted"><?= e($code) ?></span></span>
                                    <?php if ($isCurrent): ?><?= icon('check', 'h-4 w-4 text-brand-600') ?><?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </details>
            <?php endif; ?>

            <a href="<?= e($sectionBase) ?>#apply" class="btn-primary h-10 min-h-0 px-4 text-sm sm:h-11 sm:px-5 sm:text-[15px]">
                <span class="sm:hidden"><?= te('nav.cta_short') ?></span><span class="hidden sm:inline"><?= te('nav.cta') ?></span>
            </a>
        </div>
    </div>
</header>
