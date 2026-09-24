<?php
/**
 * Privacy Policy page. Text: privacy.* in lang/{code}.json.
 *
 * @var array $meta
 */

$sectionBase  = url('become-a-partner');
$mainSite     = (string) config('app.main_site_url');
$contactEmail = (string) (config('app.contact_email') ?: config('mail.reply_to'));
$emailLink    = $contactEmail !== '' ? '<a href="mailto:' . e($contactEmail) . '">' . e($contactEmail) . '</a>' : '';

$sectionIds = ['who', 'data', 'purpose', 'recipients', 'cookies', 'retention', 'rights', 'contact'];

/** Bold lead-in + text list items ([{strong, text}]). */
$richList = static function (string $key): string {
    $html = '<ul>';
    foreach (tl($key) as $item) {
        $html .= '<li><strong>' . e($item['strong'] ?? '') . '</strong> ' . e($item['text'] ?? '') . '</li>';
    }
    return $html . '</ul>';
};

require APP_ROOT . '/views/partials/head.php';
?>
<body class="min-h-screen">
<?php require APP_ROOT . '/views/partials/site-header.php'; ?>

<main id="main">
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-page px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <p class="eyebrow"><?= te('privacy.eyebrow') ?></p>
            <h1 class="mt-3 text-[2rem] font-bold leading-tight tracking-[-0.02em] text-ink sm:text-[2.5rem]"><?= te('privacy.title') ?></h1>
            <p class="mt-4 max-w-2xl text-pretty text-base leading-relaxed text-ink-muted sm:text-[17px]"><?= te('privacy.intro') ?></p>
            <p class="mt-4 text-sm text-ink-muted"><?= te('privacy.last_updated', ['date' => t('privacy.date')]) ?></p>
        </div>
    </div>

    <div class="mx-auto grid max-w-page grid-cols-1 gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-[minmax(0,15rem)_minmax(0,1fr)] lg:gap-16 lg:px-8">
        <nav aria-label="<?= te('privacy.toc') ?>" class="lg:sticky lg:top-28 lg:self-start">
            <p class="text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted"><?= te('privacy.toc') ?></p>
            <ul class="mt-3 space-y-1 border-l border-line text-[15px]">
                <?php foreach ($sectionIds as $id): ?>
                    <li><a href="#<?= e($id) ?>" class="-ml-px block border-l border-transparent py-1 pl-4 text-ink-soft hover:border-brand-500 hover:text-brand-600"><?= te("privacy.{$id}.title") ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <article class="max-w-3xl space-y-12 text-[16px] leading-[1.75] text-ink-soft [&_a]:font-medium [&_a]:text-brand-600 [&_a]:underline [&_a]:decoration-brand-200 [&_a]:underline-offset-2 hover:[&_a]:decoration-brand-500 [&_h2]:text-xl [&_h2]:font-bold [&_h2]:tracking-[-0.01em] [&_h2]:text-ink [&_h3]:mt-6 [&_h3]:font-semibold [&_h3]:text-ink [&_li]:pl-1 [&_p]:mt-3 [&_strong]:font-semibold [&_strong]:text-ink [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:space-y-1.5 [&_ul]:pl-5">

            <section id="who">
                <h2><?= te('privacy.who.title') ?></h2>
                <p><?= te('privacy.who.intro') ?></p>
                <p>
                    <strong>Khan Travel Services</strong><br>
                    <?php if ($mainSite !== ''): ?><?= te('privacy.who.website') ?>: <a href="<?= e($mainSite) ?>"><?= e(preg_replace('#^https?://#', '', $mainSite)) ?></a><br><?php endif; ?>
                    <?php if ($emailLink !== ''): ?><?= te('privacy.who.email') ?>: <?= $emailLink ?><?php endif; ?>
                </p>
            </section>

            <section id="data">
                <h2><?= te('privacy.data.title') ?></h2>
                <h3><?= te('privacy.data.form_title') ?></h3>
                <p><?= te('privacy.data.form_intro') ?></p>
                <ul>
                    <?php foreach (tl('privacy.data.form_items') as $item): ?>
                        <li><?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
                <h3><?= te('privacy.data.tech_title') ?></h3>
                <p><?= te('privacy.data.tech_text') ?></p>
            </section>

            <section id="purpose">
                <h2><?= te('privacy.purpose.title') ?></h2>
                <?= $richList('privacy.purpose.items') ?>
                <p><?= te('privacy.purpose.outro') ?></p>
            </section>

            <section id="recipients">
                <h2><?= te('privacy.recipients.title') ?></h2>
                <p><?= te('privacy.recipients.intro') ?></p>
                <?= $richList('privacy.recipients.items') ?>
                <p><?= te('privacy.recipients.outro') ?></p>
            </section>

            <section id="cookies">
                <h2><?= te('privacy.cookies.title') ?></h2>
                <?php foreach (tl('privacy.cookies.paragraphs') as $paragraph): ?>
                    <p><?= e($paragraph) ?></p>
                <?php endforeach; ?>
            </section>

            <section id="retention">
                <h2><?= te('privacy.retention.title') ?></h2>
                <?php foreach (tl('privacy.retention.paragraphs') as $paragraph): ?>
                    <p><?= e($paragraph) ?></p>
                <?php endforeach; ?>
            </section>

            <section id="rights">
                <h2><?= te('privacy.rights.title') ?></h2>
                <p><?= te('privacy.rights.intro') ?></p>
                <ul>
                    <?php foreach (tl('privacy.rights.items') as $item): ?>
                        <li><?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><?= te('privacy.rights.outro') ?></p>
            </section>

            <section id="contact">
                <h2><?= te('privacy.contact.title') ?></h2>
                <p><?= th('privacy.contact.text', ['email' => $emailLink]) ?></p>
                <p><?= te('privacy.contact.update') ?></p>
            </section>

            <div class="rounded-xl border border-line bg-surface p-5 sm:p-6">
                <p class="!mt-0 font-semibold text-ink"><?= te('privacy.cta.title') ?></p>
                <p class="!mt-1 text-[15px]"><?= te('privacy.cta.text') ?></p>
                <a href="<?= e(url('become-a-partner')) ?>#apply" class="btn-primary mt-4 h-11 px-5 !text-white !no-underline"><?= te('privacy.cta.button') ?></a>
            </div>
        </article>
    </div>
</main>

<?php require APP_ROOT . '/views/partials/site-footer.php'; ?>
</body>
</html>
