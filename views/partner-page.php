<?php
/**
 * Khan Travel B2B partner landing page.
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

$benefits = [
    ['globe', 'B2B Booking Platform', 'Search and book flights, hotels, tours, Umrah and visa services from one platform built for agencies.'],
    ['user-plus', 'Easy Partner Onboarding', 'Apply in a few minutes. Once you are approved, we help your team get set up on the platform.'],
    ['dashboard', 'Professional Agent Dashboard', 'Your own agent code, account overview and booking history in one clear dashboard.'],
    ['ticket', 'Manage Your Bookings', 'Follow every booking from request to confirmation, with traveller details and status in one list.'],
    ['briefcase', 'Travel Business Tools', 'Agent wallet, top-up requests and booking records that support your daily operations.'],
    ['headset', 'Dedicated Partner Support', 'Talk to a team that works with travel agencies every day and understands B2B travel.'],
];

$steps = [
    ['01', 'Submit Your Details', 'Tell us about your travel agency and business.'],
    ['02', 'Application Review', 'Our team reviews your partner application.'],
    ['03', 'Start Your B2B Journey', 'Once approved, your team can start using the Khan Travel B2B platform.'],
];

$selectedContact = $old['previous_contact'] ?? 'none';

require APP_ROOT . '/views/partials/head.php';
?>
<body class="min-h-screen">
<?php require APP_ROOT . '/views/partials/site-header.php'; ?>

<main id="main">

    <!-- ============================== HERO ============================== -->
    <section class="relative overflow-hidden" aria-labelledby="hero-title">
        <div class="pointer-events-none absolute inset-0 bg-dots opacity-40 [mask-image:radial-gradient(ellipse_75%_70%_at_75%_20%,#000_20%,transparent_75%)]" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -right-40 -top-48 h-[560px] w-[760px] rounded-full bg-brand-50 opacity-80 blur-3xl" aria-hidden="true"></div>

        <div class="relative mx-auto grid grid-cols-1 max-w-page items-center gap-14 px-4 pb-20 pt-10 sm:px-6 sm:pt-14 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.02fr)] lg:gap-12 lg:px-8 lg:pb-28 lg:pt-20">
            <div class="fade-up">
                <p class="inline-flex max-w-full items-center gap-2 rounded-full border border-brand-100 bg-white/80 py-1 pl-1 pr-3 text-[13px] font-medium text-brand-700 shadow-[0_1px_2px_rgba(14,28,43,0.04)]">
                    <span class="inline-flex flex-none items-center gap-1 rounded-full bg-brand-500 py-0.5 pl-1.5 pr-2.5 text-xs font-bold tracking-wide text-white"><?= icon('check', 'h-3.5 w-3.5') ?>IATA Certified</span>
                    <span class="truncate">Khan Travel B2B Partner Program</span>
                </p>

                <h1 id="hero-title" class="mt-6 text-balance text-[2.125rem] font-bold leading-[1.1] tracking-[-0.03em] text-ink sm:text-[2.75rem] lg:text-[3.25rem]">
                    Become a Khan Travel <span class="text-brand-500">B2B&nbsp;Partner</span>
                </h1>

                <p class="mt-5 max-w-xl text-pretty text-lg leading-relaxed text-ink-soft sm:text-[1.1875rem]">
                    Grow your travel business with a powerful B2B booking platform built for modern travel agencies and agents.
                </p>
                <p class="mt-3 max-w-xl text-pretty text-[15px] leading-relaxed text-ink-muted">
                    Submit your details to apply for a Khan Travel B2B partnership. Our team will review your application and contact you regarding the next steps.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="#apply" class="btn-primary group h-12 px-6 text-base">
                        Become a Partner
                        <?= icon('arrow-right', 'h-[18px] w-[18px] transition-transform duration-150 group-hover:translate-x-0.5') ?>
                    </a>
                    <a href="#how-it-works" class="btn-secondary h-12 px-6 text-base">How It Works</a>
                </div>

                <div class="mt-8 inline-flex max-w-full items-center gap-4 rounded-2xl border border-line bg-white py-3 pl-3 pr-5 shadow-card">
                    <img src="<?= e(asset('images/iata-logo.png')) ?>" alt="IATA – International Air Transport Association" width="160" height="160" class="h-14 w-14 flex-none sm:h-16 sm:w-16">
                    <div class="min-w-0 leading-snug">
                        <p class="flex items-center gap-1.5 text-base font-bold text-ink sm:text-[17px]">
                            IATA Certified <?= icon('shield-check', 'h-[18px] w-[18px] flex-none text-brand-500') ?>
                        </p>
                        <p class="mt-0.5 text-[13px] text-ink-muted sm:text-sm">Book with an internationally recognised travel company</p>
                    </div>
                </div>

                <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-2.5 text-sm font-medium text-ink-soft">
                    <li class="flex items-center gap-2"><?= icon('clock', 'h-[18px] w-[18px] text-brand-500') ?>Takes about 2 minutes</li>
                    <li class="flex items-center gap-2"><?= icon('users', 'h-[18px] w-[18px] text-brand-500') ?>Reviewed by our team</li>
                    <li class="flex items-center gap-2"><?= icon('lock', 'h-[18px] w-[18px] text-brand-500') ?>Secure submission</li>
                </ul>
            </div>

            <!-- Decorative product preview -->
            <div class="fade-up fade-up-delay relative mx-auto w-full max-w-[580px] lg:mr-0" aria-hidden="true">
                <div class="relative overflow-hidden rounded-2xl border border-line bg-white shadow-lift">
                    <div class="flex items-center justify-between gap-3 border-b border-line px-4 py-3 sm:px-5">
                        <div class="flex min-w-0 items-center gap-2.5">
                            <span class="grid h-8 w-8 flex-none place-items-center rounded-lg bg-brand-500 text-white"><?= icon('dashboard', 'h-4 w-4') ?></span>
                            <div class="min-w-0 leading-tight">
                                <p class="truncate text-[13px] font-semibold text-ink">Agent Dashboard</p>
                                <p class="truncate text-[11px] text-ink-muted">Khan Travel B2B &middot; AGT-0142</p>
                            </div>
                        </div>
                        <div class="flex flex-none items-center gap-2">
                            <span class="hidden rounded-md bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700 ring-1 ring-emerald-600/15 sm:inline-block">Active partner</span>
                            <span class="grid h-8 w-8 place-items-center rounded-full text-ink-muted ring-1 ring-line"><?= icon('bell', 'h-4 w-4') ?></span>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="hidden w-14 flex-none flex-col items-center gap-1.5 border-r border-line bg-surface py-4 sm:flex">
                            <span class="grid h-9 w-9 place-items-center rounded-lg bg-white text-brand-500 shadow-[0_1px_3px_rgba(14,28,43,0.1)]"><?= icon('home', 'h-[18px] w-[18px]') ?></span>
                            <?php foreach (['plane', 'bed', 'ticket', 'wallet', 'users'] as $nav): ?>
                                <span class="grid h-9 w-9 place-items-center rounded-lg text-slate-400"><?= icon($nav, 'h-[18px] w-[18px]') ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="min-w-0 flex-1 p-3.5 sm:p-5">
                            <div class="flex items-center gap-1 rounded-xl border border-line bg-white p-1.5 shadow-[0_1px_2px_rgba(14,28,43,0.04)]">
                                <div class="flex min-w-0 flex-1 items-center gap-2.5 px-2">
                                    <?= icon('plane', 'h-4 w-4 flex-none text-brand-500') ?>
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-medium uppercase tracking-wide text-ink-muted">Route</p>
                                        <p class="truncate text-[13px] font-semibold text-ink">LHE &rarr; DXB</p>
                                    </div>
                                </div>
                                <span class="hidden h-8 w-px bg-line min-[400px]:block"></span>
                                <div class="hidden px-2.5 min-[400px]:block">
                                    <p class="text-[10px] font-medium uppercase tracking-wide text-ink-muted">Departure</p>
                                    <p class="whitespace-nowrap text-[13px] font-semibold text-ink">12 Oct</p>
                                </div>
                                <span class="hidden h-8 w-px bg-line md:block"></span>
                                <div class="hidden px-2.5 md:block">
                                    <p class="text-[10px] font-medium uppercase tracking-wide text-ink-muted">Travellers</p>
                                    <p class="whitespace-nowrap text-[13px] font-semibold text-ink">2 Adults</p>
                                </div>
                                <span class="grid h-9 w-9 flex-none place-items-center rounded-lg bg-brand-500 text-white"><?= icon('search', 'h-4 w-4') ?></span>
                            </div>

                            <div class="mt-3.5 grid grid-cols-3 gap-2 sm:gap-2.5">
                                <div class="rounded-lg border border-line p-2.5 sm:p-3">
                                    <p class="truncate text-[11px] text-ink-muted">Bookings</p>
                                    <p class="mt-0.5 text-base font-semibold tracking-tight text-ink sm:text-[17px]">128</p>
                                    <p class="text-[10px] font-medium text-emerald-600 sm:text-[11px]">+12% this month</p>
                                </div>
                                <div class="rounded-lg border border-line p-2.5 sm:p-3">
                                    <p class="truncate text-[11px] text-ink-muted">Wallet</p>
                                    <p class="mt-0.5 text-base font-semibold tracking-tight text-ink sm:text-[17px]">482K</p>
                                    <p class="text-[10px] font-medium text-ink-muted sm:text-[11px]">PKR balance</p>
                                </div>
                                <div class="rounded-lg border border-line p-2.5 sm:p-3">
                                    <p class="truncate text-[11px] text-ink-muted">Pending</p>
                                    <p class="mt-0.5 text-base font-semibold tracking-tight text-ink sm:text-[17px]">6</p>
                                    <p class="text-[10px] font-medium text-amber-600 sm:text-[11px]">Needs action</p>
                                </div>
                            </div>

                            <div class="mt-3.5 rounded-xl border border-line">
                                <div class="flex items-center justify-between border-b border-line px-3.5 py-2.5">
                                    <p class="text-xs font-semibold text-ink">Recent bookings</p>
                                    <p class="text-[11px] font-semibold text-brand-600">View all</p>
                                </div>
                                <ul class="divide-y divide-line">
                                    <?php
                                    $rows = [
                                        ['plane', 'LHE &rarr; DXB &middot; EK 623', '2 Adults &middot; 12 Oct', 'Confirmed', 'bg-emerald-50 text-emerald-700 ring-emerald-600/15'],
                                        ['bed', 'Makkah Hotel &middot; 5 nights', '1 Room &middot; 18 Oct', 'Confirmed', 'bg-emerald-50 text-emerald-700 ring-emerald-600/15'],
                                        ['ticket', 'Umrah Package &middot; 14 days', '4 Travellers &middot; 02 Nov', 'Pending', 'bg-amber-50 text-amber-700 ring-amber-600/20'],
                                    ];
                                    foreach ($rows as [$ico, $title, $sub, $status, $chip]): ?>
                                        <li class="flex items-center gap-3 px-3.5 py-2.5">
                                            <span class="grid h-8 w-8 flex-none place-items-center rounded-lg bg-brand-50 text-brand-600"><?= icon($ico, 'h-4 w-4') ?></span>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-[12.5px] font-semibold text-ink"><?= $title ?></p>
                                                <p class="truncate text-[11px] text-ink-muted"><?= $sub ?></p>
                                            </div>
                                            <span class="flex-none rounded-md px-2 py-0.5 text-[11px] font-semibold ring-1 <?= $chip ?>"><?= $status ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-10 left-3 rounded-xl border border-line bg-white py-3 pl-3 pr-5 shadow-lift sm:-bottom-11 sm:-left-7">
                    <div class="flex items-center gap-3">
                        <span class="grid h-9 w-9 flex-none place-items-center rounded-full bg-emerald-50 text-emerald-600"><?= icon('check-circle', 'h-5 w-5') ?></span>
                        <div class="leading-snug">
                            <p class="whitespace-nowrap text-[13px] font-semibold text-ink">Application approved</p>
                            <p class="mt-0.5 whitespace-nowrap text-xs text-ink-muted">Your B2B account is ready</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================== AIRLINES ============================== -->
    <?php
    // [file, name, display height class] – heights balance square marks against wide wordmarks.
    $airlines = [
        ['emirates.png', 'Emirates', 'h-12 sm:h-14'],
        ['qatar-airways.png', 'Qatar Airways', 'h-9 sm:h-10'],
        ['turkish-airlines.png', 'Turkish Airlines', 'h-12 sm:h-14'],
        ['etihad-airways.png', 'Etihad Airways', 'h-9 sm:h-10'],
        ['air-canada.png', 'Air Canada', 'h-12 sm:h-14'],
        ['american-airlines.png', 'American Airlines', 'h-6 sm:h-7'],
    ];
    ?>
    <section class="border-t border-line bg-white py-10 sm:py-12" aria-labelledby="airlines-title">
        <div class="mx-auto max-w-page px-4 sm:px-6 lg:px-8">
            <p id="airlines-title" class="text-center text-sm font-semibold text-ink-muted">Book flights with leading airlines worldwide</p>
        </div>

        <div class="airline-marquee group relative mt-8 overflow-hidden [mask-image:linear-gradient(to_right,transparent,#000_8%,#000_92%,transparent)]">
            <ul class="airline-track flex w-max items-center group-hover:[animation-play-state:paused]">
                <?php for ($copy = 0; $copy < 4; $copy++): // 4 copies keep the loop seamless on wide screens ?>
                    <?php foreach ($airlines as [$file, $name, $height]): ?>
                        <li class="flex h-16 flex-none items-center px-7 sm:px-10"<?= $copy > 0 ? ' aria-hidden="true"' : '' ?>>
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
                <p class="eyebrow">Partner benefits</p>
                <h2 id="benefits-title" class="section-title mt-3 text-balance">Everything You Need to Grow Your Travel Business</h2>
                <p class="section-lead mt-4 text-pretty">One platform to book, manage and grow your agency's travel sales, with the Khan Travel team behind you.</p>
            </div>

            <ul class="mt-12 grid grid-cols-1 gap-px overflow-hidden rounded-2xl border border-line bg-line sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($benefits as [$ico, $title, $text]): ?>
                    <li class="bg-white p-6 transition-colors duration-200 hover:bg-surface/70 sm:p-7">
                        <span class="inline-grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-brand-600 ring-1 ring-inset ring-brand-100"><?= icon($ico, 'h-[22px] w-[22px]') ?></span>
                        <h3 class="mt-5 text-[17px] font-semibold tracking-[-0.01em] text-ink"><?= e($title) ?></h3>
                        <p class="mt-2 text-[15px] leading-relaxed text-ink-muted"><?= e($text) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <!-- ============================== HOW IT WORKS ============================== -->
    <section id="how-it-works" class="border-y border-line bg-surface py-16 sm:py-20 lg:py-24" aria-labelledby="how-title">
        <div class="mx-auto max-w-page px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="eyebrow">How it works</p>
                <h2 id="how-title" class="section-title mt-3">Three simple steps to partnership</h2>
                <p class="section-lead mt-4 text-pretty">A short application, a personal review, and your agency is ready to book.</p>
            </div>

            <ol class="mt-12 grid grid-cols-1 gap-0 lg:grid-cols-3 lg:gap-8">
                <?php foreach ($steps as $i => [$num, $title, $text]):
                    $last = $i === count($steps) - 1; ?>
                    <li class="relative flex gap-5 pb-10 last:pb-0 lg:block lg:pb-0">
                        <?php if (!$last): ?>
                            <span class="absolute bottom-0 left-6 top-12 w-px bg-brand-200 lg:hidden" aria-hidden="true"></span>
                        <?php endif; ?>
                        <div class="flex flex-none items-center gap-4">
                            <span class="relative grid h-12 w-12 place-items-center rounded-full text-[15px] font-bold tabular-nums <?= $last ? 'bg-brand-500 text-white shadow-[0_4px_12px_-2px_rgba(0,119,190,0.45)]' : 'border border-brand-200 bg-white text-brand-600 shadow-[0_1px_2px_rgba(14,28,43,0.05)]' ?>">
                                <span class="sr-only">Step </span><?= e($num) ?>
                            </span>
                            <?php if (!$last): ?>
                                <span class="hidden h-px flex-1 bg-brand-200 lg:block" aria-hidden="true"></span>
                            <?php endif; ?>
                        </div>
                        <div class="pt-2.5 lg:pt-0">
                            <h3 class="text-lg font-semibold tracking-[-0.01em] text-ink lg:mt-6"><?= e($title) ?></h3>
                            <p class="mt-2 max-w-sm text-[15px] leading-relaxed text-ink-muted"><?= e($text) ?></p>
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
                <p class="eyebrow">Partner application</p>
                <h2 id="apply-title" class="section-title mt-3">Become a Khan Travel Partner</h2>
                <p class="section-lead mt-4">Complete the form below and our team will review your application.</p>

                <ul class="mt-8 space-y-6">
                    <li class="flex gap-4">
                        <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-brand-50 text-brand-600"><?= icon('clock', 'h-5 w-5') ?></span>
                        <div>
                            <p class="font-semibold text-ink">Quick to complete</p>
                            <p class="mt-1 text-[15px] leading-relaxed text-ink-muted">A few short fields. It takes about two minutes.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-brand-50 text-brand-600"><?= icon('shield-check', 'h-5 w-5') ?></span>
                        <div>
                            <p class="font-semibold text-ink">Your data stays private</p>
                            <p class="mt-1 text-[15px] leading-relaxed text-ink-muted">We only use your details to review your application and contact you.</p>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-brand-50 text-brand-600"><?= icon('mail', 'h-5 w-5') ?></span>
                        <div>
                            <p class="font-semibold text-ink">Confirmation by email</p>
                            <p class="mt-1 text-[15px] leading-relaxed text-ink-muted">You receive a confirmation right away, and we contact you about the next steps.</p>
                        </div>
                    </li>
                </ul>

                <?php if ($contactEmail !== ''): ?>
                    <p class="mt-8 rounded-xl border border-line bg-surface p-4 text-[15px] leading-relaxed text-ink-muted">
                        Questions before you apply? Email us at
                        <a class="font-semibold text-brand-600 underline decoration-brand-200 underline-offset-2 hover:decoration-brand-500" href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a>.
                    </p>
                <?php endif; ?>
            </div>

            <div class="min-w-0 rounded-2xl border border-line bg-white shadow-card">
                <div id="form-panel"<?= $success ? ' hidden' : '' ?>>
                    <form id="partner-form" class="relative p-5 sm:p-8" action="<?= e(url('become-a-partner')) ?>" method="post" novalidate
                          data-captcha="<?= $captchaOn ? 'on' : 'off' ?>">
                        <?= csrf_field() ?>

                        <!-- Honeypot: hidden from people, filled in by bots -->
                        <div class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                            <!-- Neutral name/label so browser autofill never fills it for real visitors -->
                            <label for="kt_hp_check">Leave this field empty</label>
                            <input id="kt_hp_check" name="kt_hp_check" type="text" tabindex="-1" autocomplete="off"
                                   data-lpignore="true" data-1p-ignore data-form-type="other" value="">
                        </div>

                        <div id="form-alert" class="mb-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm leading-relaxed text-red-800" role="alert" tabindex="-1"<?= $alert ? '' : ' hidden' ?>>
                            <?= icon('alert-circle', 'mt-px h-5 w-5 flex-none text-red-600') ?>
                            <p data-alert-text><?= e($alert ?? '') ?></p>
                        </div>

                        <p class="text-[13px] text-ink-muted">Fields marked with <span class="font-semibold text-brand-600" aria-hidden="true">*</span><span class="sr-only">an asterisk</span> are required.</p>

                        <fieldset class="mt-6">
                            <legend class="flex items-center gap-2 text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted"><?= icon('building', 'h-4 w-4') ?>Company details</legend>
                            <div class="mt-4">
                                <?= text_field('company_name', 'Company', [
                                    'placeholder'  => 'Company incl. legal form (e.g. Ltd.)',
                                    'help'         => 'Registered name of your company',
                                    'autocomplete' => 'organization',
                                    'maxlength'    => 255,
                                    'value'        => $old['company_name'] ?? '',
                                    'error'        => $errors['company_name'] ?? null,
                                ]) ?>
                            </div>
                        </fieldset>

                        <fieldset class="mt-8 border-t border-line pt-7">
                            <legend class="float-left flex w-full items-center gap-2 text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted"><?= icon('user', 'h-4 w-4') ?>Contact person</legend>
                            <div class="clear-both grid grid-cols-1 gap-5 pt-4 sm:grid-cols-2">
                                <?= text_field('first_name', 'First Name', [
                                    'autocomplete' => 'given-name',
                                    'maxlength'    => 100,
                                    'value'        => $old['first_name'] ?? '',
                                    'error'        => $errors['first_name'] ?? null,
                                ]) ?>
                                <?= text_field('last_name', 'Last Name', [
                                    'autocomplete' => 'family-name',
                                    'maxlength'    => 100,
                                    'value'        => $old['last_name'] ?? '',
                                    'error'        => $errors['last_name'] ?? null,
                                ]) ?>
                            </div>
                            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <?= text_field('email', 'Business Email Address', [
                                    'type'         => 'email',
                                    'placeholder'  => 'name@youragency.com',
                                    'help'         => 'Please use an email address you can access.',
                                    'autocomplete' => 'email',
                                    'inputmode'    => 'email',
                                    'maxlength'    => 254,
                                    'value'        => $old['email'] ?? '',
                                    'error'        => $errors['email'] ?? null,
                                ]) ?>
                                <?= text_field('phone', 'Phone Number', [
                                    'type'         => 'tel',
                                    'placeholder'  => '+92 300 1234567',
                                    'help'         => 'Include your country code. WhatsApp number is fine.',
                                    'autocomplete' => 'tel',
                                    'inputmode'    => 'tel',
                                    'maxlength'    => 30,
                                    'value'        => $old['phone'] ?? '',
                                    'error'        => $errors['phone'] ?? null,
                                ]) ?>
                            </div>

                            <?php $contactError = $errors['previous_contact'] ?? null; ?>
                            <div class="mt-5" data-field="previous_contact">
                                <label for="previous_contact" class="field-label">Have you already had contact with one of our employees?<span class="field-required" aria-hidden="true">*</span></label>
                                <div class="relative">
                                    <select id="previous_contact" name="previous_contact" class="field-control" required aria-describedby="previous_contact-error"<?= $contactError ? ' aria-invalid="true"' : '' ?>>
                                        <?php foreach (PREVIOUS_CONTACT_OPTIONS as $value => $label): ?>
                                            <option value="<?= e($value) ?>"<?= $selectedContact === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?= icon('chevron-down', 'pointer-events-none absolute right-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-ink-muted') ?>
                                </div>
                                <?= field_error_html('previous_contact', $contactError) ?>
                            </div>
                        </fieldset>

                        <fieldset class="mt-8 border-t border-line pt-7">
                            <legend class="float-left flex w-full items-center gap-2 text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted"><?= icon('shield-check', 'h-4 w-4') ?>Legal Requirements</legend>
                            <div class="clear-both space-y-4 pt-4">
                                <?= checkbox_field(
                                    'authorized_representative',
                                    'I confirm that I am authorized to represent the company stated above.',
                                    ($old['authorized_representative'] ?? '') === '1',
                                    $errors['authorized_representative'] ?? null
                                ) ?>
                                <?php
                                $privacyLink = $privacyUrl !== ''
                                    ? '<a href="' . e($privacyUrl) . '" target="_blank" rel="noopener" class="font-semibold text-brand-600 underline decoration-brand-200 underline-offset-2 hover:decoration-brand-500">Privacy Policy<span class="sr-only"> (opens in a new tab)</span></a>'
                                    : '<span class="font-semibold">Privacy Policy</span>';
                                echo checkbox_field(
                                    'privacy_consent',
                                    'I hereby confirm my consent to the collection and use of my personal data according to the ' . $privacyLink . '.',
                                    ($old['privacy_consent'] ?? '') === '1',
                                    $errors['privacy_consent'] ?? null
                                );
                                ?>
                            </div>
                        </fieldset>

                        <?php $showCaptchaPlaceholder = !$captchaOn && !is_production() && config('captcha.show_placeholder'); ?>
                        <div class="<?= $captchaOn || $showCaptchaPlaceholder ? 'mt-7' : '' ?>" data-field="captcha">
                            <?php if ($captchaOn): ?>
                                <div class="frc-captcha" data-sitekey="<?= e(config('captcha.site_key')) ?>" data-start="focus" data-lang="en"
                                     data-api-endpoint="<?= e(config('captcha.endpoint')) ?>"></div>
                            <?php elseif ($showCaptchaPlaceholder): ?>
                                <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 p-4 text-[13px] leading-relaxed text-amber-900">
                                    <p class="font-semibold">CAPTCHA placeholder (development only)</p>
                                    <p class="mt-1">Friendly Captcha is not configured. Set <code class="rounded bg-amber-100 px-1">FRIENDLY_CAPTCHA_SITE_KEY</code> and <code class="rounded bg-amber-100 px-1">FRIENDLY_CAPTCHA_SECRET_KEY</code> in <code class="rounded bg-amber-100 px-1">.env</code> to enable bot protection.</p>
                                </div>
                            <?php endif; ?>
                            <?= field_error_html('captcha', $errors['captcha'] ?? null) ?>
                        </div>

                        <button type="submit" id="submit-button" class="btn-primary mt-7 h-[52px] w-full text-base">
                            <svg class="spin hidden h-5 w-5" data-spinner viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity="0.3" stroke-width="2.5"/>
                                <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                            </svg>
                            <span data-button-label>Submit Partner Application</span>
                        </button>
                        <p class="mt-4 flex items-center justify-center gap-1.5 text-center text-[13px] text-ink-muted">
                            <?= icon('lock', 'h-3.5 w-3.5 flex-none') ?>Your details are sent securely and reviewed by our team.
                        </p>
                        <p id="form-status" class="sr-only" aria-live="polite"></p>
                    </form>
                </div>

                <div id="success-panel" class="px-6 py-12 text-center sm:px-10 sm:py-16"<?= $success ? '' : ' hidden' ?>>
                    <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-50 text-emerald-600 ring-8 ring-emerald-50/60"><?= icon('check-circle', 'h-8 w-8') ?></span>
                    <h3 id="success-title" class="mt-7 text-2xl font-bold tracking-[-0.02em] text-ink outline-none" tabindex="-1">Application Submitted Successfully</h3>
                    <p class="mx-auto mt-3 max-w-md text-pretty text-[15px] leading-relaxed text-ink-muted">
                        Thank you for your interest in becoming a Khan Travel partner. We have received your application and our team will contact you regarding the next steps.
                    </p>

                    <div class="mx-auto mt-8 max-w-sm rounded-xl border border-line bg-surface p-5 text-left">
                        <p class="text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted">What happens next</p>
                        <ol class="mt-3 space-y-3 text-[15px] text-ink-soft">
                            <li class="flex gap-3"><span class="grid h-6 w-6 flex-none place-items-center rounded-full bg-white text-xs font-bold text-brand-600 ring-1 ring-brand-200">1</span>Check your inbox for a confirmation email.</li>
                            <li class="flex gap-3"><span class="grid h-6 w-6 flex-none place-items-center rounded-full bg-white text-xs font-bold text-brand-600 ring-1 ring-brand-200">2</span>Our team reviews your application.</li>
                            <li class="flex gap-3"><span class="grid h-6 w-6 flex-none place-items-center rounded-full bg-white text-xs font-bold text-brand-600 ring-1 ring-brand-200">3</span>We contact you about the next steps.</li>
                        </ol>
                    </div>

                    <?php if ($mainSite !== ''): ?>
                        <a href="<?= e($mainSite) ?>" class="btn-secondary mt-8">Back to Khan Travel</a>
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
                    <h2 id="cta-title" class="text-balance text-[1.75rem] font-bold leading-tight tracking-[-0.02em] text-white sm:text-[2.125rem]">Ready to grow with Khan Travel?</h2>
                    <p class="mt-4 max-w-lg text-pretty text-base leading-relaxed text-brand-50 sm:text-[17px]">
                        Join our B2B partner network and give your agency a professional platform for everyday bookings.
                    </p>
                    <a href="#apply" class="btn-light mt-8 h-12 w-full px-6 text-base sm:w-auto">
                        Become a Partner <?= icon('arrow-right', 'h-[18px] w-[18px]') ?>
                    </a>
                </div>

                <ul class="grid gap-3">
                    <?php foreach ([
                        ['shield-check', 'Secure application', 'Your details are sent securely and protected against spam.'],
                        ['users', 'Reviewed by people', 'Every application is checked personally by the Khan Travel team.'],
                        ['headset', 'Partner support', 'Help from a team that works with travel agencies every day.'],
                    ] as [$ico, $title, $text]): ?>
                        <li class="flex gap-4 rounded-xl bg-white/[0.08] p-4 ring-1 ring-inset ring-white/15">
                            <span class="grid h-10 w-10 flex-none place-items-center rounded-lg bg-white text-brand-600"><?= icon($ico, 'h-5 w-5') ?></span>
                            <div>
                                <p class="font-semibold text-white"><?= e($title) ?></p>
                                <p class="mt-0.5 text-[15px] leading-relaxed text-brand-50"><?= e($text) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>
</main>

<?php require APP_ROOT . '/views/partials/site-footer.php'; ?>

<?php if ($captchaOn): ?>
<script type="module" src="https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@<?= FRIENDLY_CAPTCHA_SDK_VERSION ?>/site.min.js"
        integrity="sha384-RIhFfbtmpxvwFrmR7gzJAMsqkEUafch7jzB2xJb9/EMqKDruC1YsORfOG1CR3iEJ" crossorigin="anonymous" async defer></script>
<script nomodule src="https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@<?= FRIENDLY_CAPTCHA_SDK_VERSION ?>/site.compat.min.js"
        integrity="sha384-P/6LxANmqOibwXdFrwfDNEKsCQD4baWYTwj5wW6oZkU40Bh0a0v8t63kNETOUm2h" crossorigin="anonymous" async defer></script>
<?php endif; ?>
<script src="<?= e(asset('js/partner.js')) ?>" defer></script>
</body>
</html>
