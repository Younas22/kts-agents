<?php
/**
 * Khan Travel Services B2B partner landing page. All text comes from lang/{code}.json.
 *
 * @var array       $meta
 * @var array       $errors  field => message (server-side validation, no-JS fallback)
 * @var array       $old     previously submitted values
 * @var string|null $alert   form-level error message
 * @var bool        $success show the success state instead of the form
 */

require_once APP_ROOT . '/views/partials/form-fields.php';

$sectionBase  = '';
$privacyUrl   = privacy_policy_url();
$mainSite     = (string) config('app.main_site_url');
$contactEmail = (string) config('app.contact_email');
$captchaOn    = captcha_enabled();

$benefitIcons = ['globe', 'user-plus', 'dashboard', 'ticket', 'briefcase', 'headset'];
$applyIcons   = ['clock', 'shield-check', 'mail'];
$trustIcons   = ['clock', 'users', 'lock'];
$ctaIcons     = ['shield-check', 'users', 'headset'];

$selectedContact = $old['previous_contact'] ?? 'none';

// Texts used by assets/js/partner.js (client-side validation and states).
$jsText = [
    'messages'   => t('messages'),
    'validation' => t('validation'),
    'submit'     => t('form.submit'),
    'submitting' => t('form.submitting'),
    'status'     => t('form.submitting_status'),
];

require APP_ROOT . '/views/partials/head.php';
?>
<body class="min-h-screen">
<?php require APP_ROOT . '/views/partials/site-header.php'; ?>

<main id="main">

    <!-- ============================== HERO ============================== -->
    <section class="relative overflow-hidden bg-white" aria-labelledby="hero-title">
        <!-- xl+: the picture fills the right side; its left side is white, so the text sits on it -->
        <picture>
            <source srcset="<?= e(asset('images/hero-bg.webp')) ?>" type="image/webp">
            <img src="<?= e(asset('images/hero-bg.jpg')) ?>" alt="" width="1862" height="845" fetchpriority="high" decoding="async" aria-hidden="true"
                 class="pointer-events-none absolute right-0 top-1/2 hidden h-auto w-[88vw] max-w-[1862px] -translate-y-1/2 select-none [mask-image:linear-gradient(to_bottom,transparent,#000_8%,#000_92%,transparent)] xl:block">
        </picture>

        <div class="relative mx-auto max-w-page px-4 pb-14 pt-10 sm:px-6 sm:pt-14 lg:px-8 xl:flex xl:min-h-[max(660px,40vw)] xl:items-center xl:py-20">
            <div class="fade-up max-w-2xl xl:max-w-xl">
                <p class="inline-flex max-w-full items-center gap-2 rounded-full border border-brand-100 bg-white/80 py-1 pl-1 pr-3 text-[13px] font-medium text-brand-700 shadow-[0_1px_2px_rgba(14,28,43,0.04)]">
                    <span class="inline-flex flex-none items-center gap-1 rounded-full bg-brand-500 py-0.5 pl-1.5 pr-2.5 text-xs font-bold tracking-wide text-white"><?= icon('check', 'h-3.5 w-3.5') ?><?= te('hero.badge_iata') ?></span>
                    <span class="truncate"><?= te('hero.badge_program') ?></span>
                </p>

                <h1 id="hero-title" class="mt-6 text-balance text-[2.125rem] font-bold leading-[1.1] tracking-[-0.03em] text-ink sm:text-[2.75rem] lg:text-[3.25rem]">
                    <?= te('hero.title_start') ?> <span class="text-brand-500"><?= str_replace(' ', '&nbsp;', te('hero.title_highlight')) ?></span>
                </h1>

                <p class="mt-5 max-w-xl text-pretty text-lg leading-relaxed text-ink-soft sm:text-[1.1875rem]"><?= te('hero.lead') ?></p>
                <p class="mt-3 max-w-xl text-pretty text-[15px] leading-relaxed text-ink-muted"><?= te('hero.text') ?></p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="#apply" class="btn-primary group h-12 px-6 text-base">
                        <?= te('hero.cta_primary') ?>
                        <?= icon('arrow-right', 'h-[18px] w-[18px] transition-transform duration-150 group-hover:translate-x-0.5') ?>
                    </a>
                    <a href="#how-it-works" class="btn-secondary h-12 px-6 text-base"><?= te('hero.cta_secondary') ?></a>
                </div>

                <div class="mt-8 inline-flex max-w-full items-center gap-4 rounded-2xl border border-line bg-white py-3 pl-3 pr-5 shadow-card">
                    <img src="<?= e(asset('images/iata-logo.png')) ?>" alt="<?= te('hero.iata_alt') ?>" width="160" height="160" class="h-14 w-14 flex-none sm:h-16 sm:w-16">
                    <div class="min-w-0 leading-snug">
                        <p class="flex items-center gap-1.5 text-base font-bold text-ink sm:text-[17px]">
                            <?= te('hero.iata_title') ?> <?= icon('shield-check', 'h-[18px] w-[18px] flex-none text-brand-500') ?>
                        </p>
                        <p class="mt-0.5 text-[13px] text-ink-muted sm:text-sm"><?= te('hero.iata_text') ?></p>
                    </div>
                </div>

                <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-2.5 text-sm font-medium text-ink-soft">
                    <?php foreach (tl('hero.trust') as $i => $text): ?>
                        <li class="flex items-center gap-2"><?= icon($trustIcons[$i] ?? 'check', 'h-[18px] w-[18px] text-brand-500') ?><?= e($text) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Below xl: the right part of the picture, shown under the text -->
            <picture class="mt-10 block xl:hidden">
                <source srcset="<?= e(asset('images/hero-bg-mobile.webp')) ?>" type="image/webp">
                <img src="<?= e(asset('images/hero-bg-mobile.jpg')) ?>" alt="" width="1102" height="845" decoding="async" aria-hidden="true" class="mx-auto h-auto w-full max-w-3xl [mask-image:linear-gradient(to_bottom,transparent,#000_12%,#000_90%,transparent)]">
            </picture>
        </div>
    </section>

    <!-- ============================== AIRLINES ============================== -->
    <?php
    // [file, name, display height class] – heights balance square marks against wide wordmarks.
    $airlines = [
        ['pia.png', 'PIA – Pakistan International Airlines', 'h-11 sm:h-12'],
        ['emirates.png', 'Emirates', 'h-12 sm:h-14'],
        ['qatar-airways.png', 'Qatar Airways', 'h-9 sm:h-10'],
        ['turkish-airlines.png', 'Turkish Airlines', 'h-12 sm:h-14'],
        ['etihad-airways.png', 'Etihad Airways', 'h-9 sm:h-10'],
        ['kam-air.png', 'Kam Air', 'h-12 sm:h-14'],
        ['air-canada.png', 'Air Canada', 'h-12 sm:h-14'],
        ['american-airlines.png', 'American Airlines', 'h-6 sm:h-7'],
    ];
    ?>
    <section class="border-t border-line bg-white py-10 sm:py-12" aria-labelledby="airlines-title">
        <div class="mx-auto max-w-page px-4 sm:px-6 lg:px-8">
            <p id="airlines-title" class="text-center text-sm font-semibold text-ink-muted"><?= te('airlines.title') ?></p>
        </div>

        <div class="airline-marquee group relative mt-8 overflow-hidden [mask-image:linear-gradient(to_right,transparent,#000_8%,#000_92%,transparent)]">
            <ul class="airline-track flex w-max items-center group-hover:[animation-play-state:paused]">
                <?php for ($copy = 0; $copy < 4; $copy++): // 4 copies keep the loop seamless on wide screens ?>
                    <?php foreach ($airlines as [$file, $name, $height]): ?>
                        <li class="flex h-16 flex-none items-center px-4 sm:px-6"<?= $copy > 0 ? ' aria-hidden="true"' : '' ?>>
                            <img src="<?= e(asset('images/airlines/' . $file)) ?>" alt="<?= $copy > 0 ? '' : e($name) ?>" loading="lazy" decoding="async" class="<?= $height ?> w-auto max-w-none select-none" draggable="false">
                        </li>
                    <?php endforeach; ?>
                <?php endfor; ?>
            </ul>
        </div>
    </section>

    <!-- ============================== BENEFITS ============================== -->
    <section id="benefits" class="border-t border-line py-16 sm:py-20 lg:py-24" aria-labelledby="benefits-title">
        <div class="mx-auto max-w-page px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="eyebrow"><?= te('benefits.eyebrow') ?></p>
                <h2 id="benefits-title" class="section-title mt-3 text-balance"><?= te('benefits.title') ?></h2>
                <p class="section-lead mt-4 text-pretty"><?= te('benefits.lead') ?></p>
            </div>

            <ul class="mt-12 grid grid-cols-1 gap-px overflow-hidden rounded-2xl border border-line bg-line sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach (tl('benefits.items') as $i => $item): ?>
                    <li class="bg-white p-6 transition-colors duration-200 hover:bg-surface/70 sm:p-7">
                        <span class="inline-grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-inset ring-brand-100"><?= icon($benefitIcons[$i] ?? 'check', 'h-[22px] w-[22px]') ?></span>
                        <h3 class="mt-5 text-[17px] font-semibold tracking-[-0.01em] text-ink"><?= e($item['title'] ?? '') ?></h3>
                        <p class="mt-2 text-[15px] leading-relaxed text-ink-muted"><?= e($item['text'] ?? '') ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- ============================== HOW IT WORKS ============================== -->
    <section id="how-it-works" class="border-y border-line bg-surface py-16 sm:py-20 lg:py-24" aria-labelledby="how-title">
        <div class="mx-auto max-w-page px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="eyebrow"><?= te('how.eyebrow') ?></p>
                <h2 id="how-title" class="section-title mt-3"><?= te('how.title') ?></h2>
                <p class="section-lead mt-4 text-pretty"><?= te('how.lead') ?></p>
            </div>

            <ol class="mt-12 grid grid-cols-1 gap-0 lg:grid-cols-3 lg:gap-8">
                <?php $steps = tl('how.steps'); foreach ($steps as $i => $step):
                    $last = $i === count($steps) - 1; ?>
                    <li class="relative flex gap-5 pb-10 last:pb-0 lg:block lg:pb-0">
                        <?php if (!$last): ?>
                            <span class="absolute bottom-0 left-6 top-12 w-px bg-brand-200 lg:hidden" aria-hidden="true"></span>
                        <?php endif; ?>
                        <div class="flex flex-none items-center gap-4">
                            <span class="relative grid h-12 w-12 place-items-center rounded-full text-[15px] font-bold tabular-nums <?= $last ? 'bg-brand-500 text-white shadow-[0_4px_12px_-2px_rgba(0,119,190,0.45)]' : 'border border-brand-200 bg-white text-brand-600 shadow-[0_1px_2px_rgba(14,28,43,0.05)]' ?>">
                                <span class="sr-only"><?= te('how.step') ?> </span><?= sprintf('%02d', $i + 1) ?>
                            </span>
                            <?php if (!$last): ?>
                                <span class="hidden h-px flex-1 bg-brand-200 lg:block" aria-hidden="true"></span>
                            <?php endif; ?>
                        </div>
                        <div class="pt-2.5 lg:pt-0">
                            <h3 class="text-lg font-semibold tracking-[-0.01em] text-ink lg:mt-6"><?= e($step['title'] ?? '') ?></h3>
                            <p class="mt-2 max-w-sm text-[15px] leading-relaxed text-ink-muted"><?= e($step['text'] ?? '') ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- ============================== REGISTRATION ============================== -->
    <section id="apply" class="py-16 sm:py-20 lg:py-24" aria-labelledby="apply-title">
        <div class="mx-auto grid grid-cols-1 max-w-page gap-10 px-4 sm:px-6 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:gap-16 lg:px-8">

            <div class="lg:sticky lg:top-28 lg:self-start">
                <p class="eyebrow"><?= te('apply.eyebrow') ?></p>
                <h2 id="apply-title" class="section-title mt-3"><?= te('apply.title') ?></h2>
                <p class="section-lead mt-4"><?= te('apply.lead') ?></p>

                <ul class="mt-8 space-y-6">
                    <?php foreach (tl('apply.points') as $i => $point): ?>
                        <li class="flex gap-4">
                            <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-brand-50 text-brand-600"><?= icon($applyIcons[$i] ?? 'check', 'h-5 w-5') ?></span>
                            <div>
                                <p class="font-semibold text-ink"><?= e($point['title'] ?? '') ?></p>
                                <p class="mt-1 text-[15px] leading-relaxed text-ink-muted"><?= e($point['text'] ?? '') ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($contactEmail !== ''): ?>
                    <p class="mt-8 rounded-xl border border-line bg-surface p-4 text-[15px] leading-relaxed text-ink-muted">
                        <?= th('apply.contact', ['email' => '<a class="font-semibold text-brand-600 underline decoration-brand-200 underline-offset-2 hover:decoration-brand-500" href="mailto:' . e($contactEmail) . '">' . e($contactEmail) . '</a>']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="min-w-0 rounded-2xl border border-line bg-white shadow-card">
                <div id="form-panel"<?= $success ? ' hidden' : '' ?>>
                    <form id="partner-form" class="relative p-5 sm:p-8" action="<?= e(url('become-a-partner')) ?>" method="post" novalidate
                          data-captcha="<?= $captchaOn ? 'on' : 'off' ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="lang" value="<?= e(current_language()) ?>">

                        <!-- Honeypot: hidden from people, filled in by bots -->
                        <div class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                            <!-- Neutral name/label so browser autofill never fills it for real visitors -->
                            <label for="kt_hp_check"><?= te('form.honeypot') ?></label>
                            <input id="kt_hp_check" name="kt_hp_check" type="text" tabindex="-1" autocomplete="off"
                                   data-lpignore="true" data-1p-ignore data-form-type="other" value="">
                        </div>

                        <div id="form-alert" class="mb-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm leading-relaxed text-red-800" role="alert" tabindex="-1"<?= $alert ? '' : ' hidden' ?>>
                            <?= icon('alert-circle', 'mt-px h-5 w-5 flex-none text-red-600') ?>
                            <p data-alert-text><?= e($alert ?? '') ?></p>
                        </div>

                        <p class="text-[13px] text-ink-muted"><?= th('form.required_note', ['star' => '<span class="font-semibold text-brand-600" aria-hidden="true">*</span><span class="sr-only">' . te('form.asterisk') . '</span>']) ?></p>

                        <fieldset class="mt-6">
                            <legend class="flex items-center gap-2 text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted"><?= icon('building', 'h-4 w-4') ?><?= te('form.section_company') ?></legend>
                            <div class="mt-4">
                                <?= text_field('company_name', t('form.company.label'), [
                                    'placeholder'  => t('form.company.placeholder'),
                                    'help'         => t('form.company.help'),
                                    'autocomplete' => 'organization',
                                    'maxlength'    => 255,
                                    'value'        => $old['company_name'] ?? '',
                                    'error'        => $errors['company_name'] ?? null,
                                ]) ?>
                            </div>
                        </fieldset>

                        <fieldset class="mt-8 border-t border-line pt-7">
                            <legend class="float-left flex w-full items-center gap-2 text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted"><?= icon('user', 'h-4 w-4') ?><?= te('form.section_contact') ?></legend>
                            <div class="clear-both grid grid-cols-1 gap-5 pt-4 sm:grid-cols-2">
                                <?= text_field('first_name', t('form.first_name.label'), [
                                    'autocomplete' => 'given-name',
                                    'maxlength'    => 100,
                                    'value'        => $old['first_name'] ?? '',
                                    'error'        => $errors['first_name'] ?? null,
                                ]) ?>
                                <?= text_field('last_name', t('form.last_name.label'), [
                                    'autocomplete' => 'family-name',
                                    'maxlength'    => 100,
                                    'value'        => $old['last_name'] ?? '',
                                    'error'        => $errors['last_name'] ?? null,
                                ]) ?>
                            </div>
                            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <?= text_field('email', t('form.email.label'), [
                                    'type'         => 'email',
                                    'placeholder'  => t('form.email.placeholder'),
                                    'help'         => t('form.email.help'),
                                    'autocomplete' => 'email',
                                    'inputmode'    => 'email',
                                    'maxlength'    => 254,
                                    'value'        => $old['email'] ?? '',
                                    'error'        => $errors['email'] ?? null,
                                ]) ?>
                                <?= text_field('phone', t('form.phone.label'), [
                                    'type'         => 'tel',
                                    'placeholder'  => t('form.phone.placeholder'),
                                    'help'         => t('form.phone.help'),
                                    'autocomplete' => 'tel',
                                    'inputmode'    => 'tel',
                                    'maxlength'    => 30,
                                    'value'        => $old['phone'] ?? '',
                                    'error'        => $errors['phone'] ?? null,
                                ]) ?>
                            </div>

                            <?php $contactError = $errors['previous_contact'] ?? null; ?>
                            <div class="mt-5" data-field="previous_contact">
                                <label for="previous_contact" class="field-label"><?= te('form.previous_contact.label') ?><span class="field-required" aria-hidden="true">*</span></label>
                                <div class="relative">
                                    <select id="previous_contact" name="previous_contact" class="field-control" required aria-describedby="previous_contact-error"<?= $contactError ? ' aria-invalid="true"' : '' ?>>
                                        <?php foreach (PREVIOUS_CONTACT_OPTIONS as $value): ?>
                                            <option value="<?= e($value) ?>"<?= $selectedContact === $value ? ' selected' : '' ?>><?= e(previous_contact_label($value)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?= icon('chevron-down', 'pointer-events-none absolute right-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-muted') ?>
                                </div>
                                <?= field_error_html('previous_contact', $contactError) ?>
                            </div>
                        </fieldset>

                        <fieldset class="mt-8 border-t border-line pt-7">
                            <legend class="float-left flex w-full items-center gap-2 text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted"><?= icon('shield-check', 'h-4 w-4') ?><?= te('form.section_legal') ?></legend>
                            <div class="clear-both space-y-4 pt-4">
                                <?= checkbox_field(
                                    'authorized_representative',
                                    te('form.authorized'),
                                    ($old['authorized_representative'] ?? '') === '1',
                                    $errors['authorized_representative'] ?? null
                                ) ?>
                                <?php
                                $privacyLink = '<a href="' . e($privacyUrl) . '" target="_blank" rel="noopener" class="font-semibold text-brand-600 underline decoration-brand-200 underline-offset-2 hover:decoration-brand-500">'
                                    . te('form.privacy_link') . '<span class="sr-only"> ' . te('form.new_tab') . '</span></a>';
                                echo checkbox_field(
                                    'privacy_consent',
                                    th('form.privacy_consent', ['link' => $privacyLink]),
                                    ($old['privacy_consent'] ?? '') === '1',
                                    $errors['privacy_consent'] ?? null
                                );
                                ?>
                            </div>
                        </fieldset>

                        <?php $showCaptchaPlaceholder = !$captchaOn && !is_production() && config('captcha.show_placeholder'); ?>
                        <div class="<?= $captchaOn || $showCaptchaPlaceholder ? 'mt-7' : '' ?>" data-field="captcha">
                            <?php if ($captchaOn): ?>
                                <div class="frc-captcha" data-sitekey="<?= e(config('captcha.site_key')) ?>" data-start="focus" data-lang="<?= e(current_language()) ?>"
                                     data-api-endpoint="<?= e(config('captcha.endpoint')) ?>"></div>
                            <?php elseif ($showCaptchaPlaceholder): ?>
                                <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 p-4 text-[13px] leading-relaxed text-amber-900">
                                    <p class="font-semibold"><?= te('form.captcha_placeholder_title') ?></p>
                                    <p class="mt-1"><?= te('form.captcha_placeholder_text') ?></p>
                                </div>
                            <?php endif; ?>
                            <?= field_error_html('captcha', $errors['captcha'] ?? null) ?>
                        </div>

                        <button type="submit" id="submit-button" class="btn-primary mt-7 h-[52px] w-full text-base">
                            <svg class="spin hidden h-5 w-5" data-spinner viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity="0.3" stroke-width="2.5"/>
                                <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                            <span data-button-label><?= te('form.submit') ?></span>
                        </button>
                        <p class="mt-4 flex items-center justify-center gap-1.5 text-center text-[13px] text-ink-muted">
                            <?= icon('lock', 'h-3.5 w-3.5 flex-none') ?><?= te('form.secure_note') ?>
                        </p>
                        <p id="form-status" class="sr-only" aria-live="polite"></p>
                    </form>
                </div>

                <div id="success-panel" class="px-6 py-12 text-center sm:px-10 sm:py-16"<?= $success ? '' : ' hidden' ?>>
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-50 text-emerald-600 ring-8 ring-emerald-50/60"><?= icon('check-circle', 'h-8 w-8') ?></span>
                    <h3 id="success-title" class="mt-7 text-2xl font-bold tracking-[-0.02em] text-ink outline-none" tabindex="-1"><?= te('success.title') ?></h3>
                    <p class="mx-auto mt-3 max-w-md text-pretty text-[15px] leading-relaxed text-ink-muted"><?= te('success.text') ?></p>

                    <div class="mx-auto mt-8 max-w-sm rounded-xl border border-line bg-surface p-5 text-left">
                        <p class="text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted"><?= te('success.next_title') ?></p>
                        <ol class="mt-3 space-y-3 text-[15px] text-ink-soft">
                            <?php foreach (tl('success.next') as $i => $text): ?>
                                <li class="flex gap-3"><span class="grid h-6 w-6 flex-none place-items-center rounded-full bg-white text-xs font-bold text-brand-600 ring-1 ring-brand-200"><?= $i + 1 ?></span><?= e($text) ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>

                    <?php if ($mainSite !== ''): ?>
                        <a href="<?= e($mainSite) ?>" class="btn-secondary mt-8"><?= te('success.back') ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================== TRUST / CTA ============================== -->
    <section class="px-4 pb-16 sm:px-6 sm:pb-20 lg:px-8 lg:pb-24" aria-labelledby="cta-title">
        <div class="relative mx-auto max-w-page overflow-hidden rounded-2xl bg-brand-500 px-6 py-12 sm:px-10 sm:py-14 lg:px-14">
            <div class="pointer-events-none absolute inset-0 opacity-[0.14] [background-image:radial-gradient(#fff_1px,transparent_1px)] [background-size:22px_22px] [mask-image:linear-gradient(to_left,#000,transparent_70%)]" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/15" aria-hidden="true"></div>
            <div class="pointer-events-none absolute -right-8 -top-8 h-40 w-40 rounded-full border border-white/15" aria-hidden="true"></div>

            <div class="relative grid grid-cols-1 items-center gap-10 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1fr)] lg:gap-14">
                <div>
                    <h2 id="cta-title" class="text-balance text-[1.75rem] font-bold leading-tight tracking-[-0.02em] text-white sm:text-[2.125rem]"><?= te('cta.title') ?></h2>
                    <p class="mt-4 max-w-lg text-pretty text-base leading-relaxed text-brand-50 sm:text-[17px]"><?= te('cta.text') ?></p>
                    <a href="#apply" class="btn-light mt-8 h-12 w-full px-6 text-base sm:w-auto">
                        <?= te('cta.button') ?> <?= icon('arrow-right', 'h-[18px] w-[18px]') ?>
                    </a>
                </div>

                <ul class="grid gap-3">
                    <?php foreach (tl('cta.points') as $i => $point): ?>
                        <li class="flex gap-4 rounded-xl bg-white/[0.08] p-4 ring-1 ring-inset ring-white/15">
                            <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-white text-brand-600"><?= icon($ctaIcons[$i] ?? 'check', 'h-5 w-5') ?></span>
                            <div>
                                <p class="font-semibold text-white"><?= e($point['title'] ?? '') ?></p>
                                <p class="mt-0.5 text-[15px] leading-relaxed text-brand-50"><?= e($point['text'] ?? '') ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>
</main>

<?php require APP_ROOT . '/views/partials/site-footer.php'; ?>

<script id="kt-i18n" type="application/json"><?= json_encode($jsText, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php if ($captchaOn): ?>
<script type="module" src="https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@<?= FRIENDLY_CAPTCHA_SDK_VERSION ?>/site.min.js"
        integrity="sha384-RIhFfbtmpxvwFrmR7gzJAMsqkEUafch7jzB2xJb9/EMqKDruC1YsORfOG1CR3iEJ" crossorigin="anonymous" async defer></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@<?= FRIENDLY_CAPTCHA_SDK_VERSION ?>/site.compat.min.js"
        integrity="sha384-P/6LxANmqOibwXdFrwfDNEKsCQD4baWYTwj5wW6oZkU40Bh0a0v8t63kNETOUm2h" crossorigin="anonymous" async defer></script>
<?php endif; ?>
<script src="<?= e(asset('js/partner.js')) ?>" defer></script>
</body>
</html>
