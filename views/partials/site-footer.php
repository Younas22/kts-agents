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
            <img src="<?= e(asset('images/khan-travel-logo.png')) ?>" alt="Khan Travel" width="128" height="128" loading="lazy" class="h-12 w-12 flex-none">
            <p class="text-sm leading-relaxed text-ink-muted">
                <span class="font-semibold text-ink">Khan Travel</span><br>
                B2B Partner Program
            </p>
        </div>

        <nav aria-label="Footer">
            <ul class="flex flex-wrap gap-x-6 gap-y-3 text-sm font-medium text-ink-soft">
                <li><a class="hover:text-brand-600" href="<?= e($sectionBase) ?>#benefits">Benefits</a></li>
                <li><a class="hover:text-brand-600" href="<?= e($sectionBase) ?>#how-it-works">How It Works</a></li>
                <li><a class="hover:text-brand-600" href="<?= e($sectionBase) ?>#apply">Apply</a></li>
                <li><a class="hover:text-brand-600" href="<?= e($privacyUrl) ?>">Privacy Policy</a></li>
<?php if ($mainSite !== ''): ?>
                <li><a class="hover:text-brand-600" href="<?= e($mainSite) ?>">Khan Travel Website</a></li>
<?php endif; ?>
            </ul>
        </nav>
    </div>
    <div class="border-t border-line">
        <div class="mx-auto flex max-w-page flex-col gap-2 px-4 py-5 text-[13px] text-ink-muted sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom, 0px))">
            <p>&copy; <?= date('Y') ?> Khan Travel. All rights reserved.</p>
            <p>Powered by <a class="font-semibold text-brand-600 hover:text-brand-700 hover:underline" href="https://travelbookingpanel.com" target="_blank" rel="noopener">TravelBookingPanel</a></p>
        </div>
    </div>
</footer>
