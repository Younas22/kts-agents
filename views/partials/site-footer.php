<?php
/**
 * Site footer.
 *
 * @var string $sectionBase
 */
$sectionBase ??= '';
$privacyUrl  = privacy_policy_url();
$mainSite    = (string) config('app.main_site_url');
?>
<footer class="border-t border-line bg-white">
    <div class="mx-auto flex max-w-page flex-col gap-8 px-4 py-10 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <div class="flex items-center gap-4">
            <img src="<?= e(asset('images/khan-travel-logo.png')) ?>" alt="<?= te('site.logo_alt') ?>" width="128" height="128" loading="lazy" class="h-12 w-12 flex-none">
            <p class="text-sm leading-relaxed text-ink-muted">
                <span class="font-semibold text-ink"><?= te('site.brand') ?></span><br>
                <?= te('site.program') ?>
            </p>
        </div>

        <nav aria-label="<?= te('footer.label') ?>">
            <ul class="flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-ink-soft">
                <li><a class="hover:text-brand-600" href="<?= e($sectionBase) ?>#benefits"><?= te('nav.benefits') ?></a></li>
                <li><a class="hover:text-brand-600" href="<?= e($sectionBase) ?>#how-it-works"><?= te('nav.how') ?></a></li>
                <li><a class="hover:text-brand-600" href="<?= e($sectionBase) ?>#apply"><?= te('nav.apply') ?></a></li>
                <li><a class="hover:text-brand-600" href="<?= e($privacyUrl) ?>#imprint"><?= te('footer.imprint') ?></a></li>
                <li><a class="hover:text-brand-600" href="<?= e($privacyUrl) ?>"><?= te('footer.privacy') ?></a></li>
<?php if ($mainSite !== ''): ?>
                <li><a class="hover:text-brand-600" href="<?= e($mainSite) ?>"><?= te('footer.website') ?></a></li>
<?php endif; ?>
            </ul>
        </nav>
    </div>
    <div class="border-t border-line">
        <div class="mx-auto max-w-page px-4 py-5 text-center text-[13px] text-ink-muted sm:px-6 lg:px-8" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom, 0px))">
            <p><?= te('footer.copyright', ['year' => date('Y')]) ?></p>
        </div>
    </div>
</footer>
<script src="<?= e(asset('js/site.js')) ?>" defer></script>
