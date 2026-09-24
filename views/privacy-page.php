<?php
/**
 * Privacy Policy page.
 *
 * @var array $meta
 */

$sectionBase  = url('become-a-partner');
$mainSite     = (string) config('app.main_site_url');
$contactEmail = (string) (config('app.contact_email') ?: config('mail.reply_to'));
$lastUpdated  = '24 September 2026';

$sections = [
    ['who', '1. Who is responsible'],
    ['data', '2. What data we collect'],
    ['purpose', '3. Why we use your data'],
    ['recipients', '4. Who receives your data'],
    ['cookies', '5. Cookies and tracking'],
    ['retention', '6. How long we keep your data'],
    ['rights', '7. Your rights'],
    ['contact', '8. Contact'],
];

require APP_ROOT . '/views/partials/head.php';
?>
<body class="min-h-screen">
<?php require APP_ROOT . '/views/partials/site-header.php'; ?>

<main id="main">
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-page px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
            <p class="eyebrow">Legal</p>
            <h1 class="mt-3 text-[2rem] font-bold leading-tight tracking-[-0.02em] text-ink sm:text-[2.5rem]">Privacy Policy</h1>
            <p class="mt-4 max-w-2xl text-pretty text-base leading-relaxed text-ink-muted sm:text-[17px]">
                This policy explains how Khan Travel handles the personal data you share with us when you apply to become a B2B partner.
            </p>
            <p class="mt-4 text-sm text-ink-muted">Last updated: <?= e($lastUpdated) ?></p>
        </div>
    </div>

    <div class="mx-auto grid max-w-page grid-cols-1 gap-10 px-4 py-12 sm:px-6 sm:py-16 lg:grid-cols-[minmax(0,15rem)_minmax(0,1fr)] lg:gap-16 lg:px-8">
        <nav aria-label="On this page" class="lg:sticky lg:top-28 lg:self-start">
            <p class="text-[13px] font-semibold uppercase tracking-[0.06em] text-ink-muted">On this page</p>
            <ul class="mt-3 space-y-1 border-l border-line text-[15px]">
                <?php foreach ($sections as [$id, $title]): ?>
                    <li><a href="#<?= e($id) ?>" class="-ml-px block border-l border-transparent py-1 pl-4 text-ink-soft hover:border-brand-500 hover:text-brand-600"><?= e($title) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <article class="max-w-3xl space-y-12 text-[16px] leading-[1.75] text-ink-soft [&_a]:font-medium [&_a]:text-brand-600 [&_a]:underline [&_a]:decoration-brand-200 [&_a]:underline-offset-2 hover:[&_a]:decoration-brand-500 [&_h2]:text-xl [&_h2]:font-bold [&_h2]:tracking-[-0.01em] [&_h2]:text-ink [&_h3]:mt-6 [&_h3]:font-semibold [&_h3]:text-ink [&_li]:pl-1 [&_p]:mt-3 [&_strong]:font-semibold [&_strong]:text-ink [&_ul]:mt-3 [&_ul]:list-disc [&_ul]:space-y-1.5 [&_ul]:pl-5">

            <section id="who">
                <h2>1. Who is responsible</h2>
                <p>The controller responsible for processing your personal data on this website is:</p>
                <p>
                    <strong>Khan Travel</strong><br>
                    <?php if ($mainSite !== ''): ?>Website: <a href="<?= e($mainSite) ?>"><?= e(preg_replace('#^https?://#', '', $mainSite)) ?></a><br><?php endif; ?>
                    <?php if ($contactEmail !== ''): ?>Email: <a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a><?php endif; ?>
                </p>
            </section>

            <section id="data">
                <h2>2. What data we collect</h2>
                <h3>Partner application form</h3>
                <p>When you apply to become a partner, we collect the information you enter:</p>
                <ul>
                    <li>Company name, including its legal form</li>
                    <li>First and last name of the contact person</li>
                    <li>Business email address and phone number</li>
                    <li>Whether you have already been in contact with one of our employees</li>
                    <li>Your confirmation that you may represent the company, and your consent to this policy</li>
                    <li>Date and time of your application</li>
                </ul>
                <h3>Technical data</h3>
                <p>To keep the form secure and protect it against spam and abuse, we process your IP address for a short time. It is used to limit repeated submission attempts and may appear in our security logs. When you visit the page, our web server also records standard access data (such as time of access and browser type) in its log files.</p>
            </section>

            <section id="purpose">
                <h2>3. Why we use your data</h2>
                <ul>
                    <li><strong>To review your application and contact you</strong> about a possible partnership. This is necessary to take steps at your request before entering into a contract (Art. 6(1)(b) GDPR).</li>
                    <li><strong>To send you a confirmation email</strong> and to notify our team about your application (Art. 6(1)(b) GDPR).</li>
                    <li><strong>To keep this website secure</strong> and prevent spam, fraud and abuse. This is our legitimate interest (Art. 6(1)(f) GDPR).</li>
                    <li><strong>Based on your consent</strong>, which you give with the checkbox in the form (Art. 6(1)(a) GDPR). You can withdraw your consent at any time with effect for the future.</li>
                </ul>
                <p>If your application is approved, your details are used to set up your partner account on our B2B booking platform. We do not sell your data and do not use it for advertising without your permission.</p>
            </section>

            <section id="recipients">
                <h2>4. Who receives your data</h2>
                <p>Your application is only seen by the Khan Travel team responsible for partner applications. We also work with carefully selected service providers who process data on our behalf:</p>
                <ul>
                    <li><strong>Hosting:</strong> our web hosting provider stores the website and the application database.</li>
                    <li><strong>Email delivery:</strong> confirmation and notification emails are sent through Resend (Resend, Inc., USA). Where data is transferred outside the EU/EEA, this is based on appropriate safeguards such as the EU Standard Contractual Clauses.</li>
                    <li><strong>Bot protection:</strong> if activated, the form uses Friendly Captcha (Friendly Captcha GmbH, Germany) to check that submissions come from real people. It works without cookies and without tracking.</li>
                </ul>
                <p>We only pass on data to authorities if we are legally required to do so.</p>
            </section>

            <section id="cookies">
                <h2>5. Cookies and tracking</h2>
                <p>This page only uses one technically necessary session cookie. It protects the form against misuse (for example, cross-site request forgery) and is deleted when you close your browser. We do not use analytics, advertising or tracking cookies.</p>
                <p>Fonts and scripts are loaded from our own server, so no data is sent to font providers such as Google Fonts.</p>
            </section>

            <section id="retention">
                <h2>6. How long we keep your data</h2>
                <p>If your application is approved, we keep your data for as long as the partnership exists, and afterwards as long as legal retention periods (for example under commercial and tax law) require.</p>
                <p>If your application does not lead to a partnership, we delete your data as soon as it is no longer needed, at the latest 12 months after our decision, unless you have agreed to a longer storage period. Security data such as IP-based rate-limit entries are deleted automatically after a short time.</p>
            </section>

            <section id="rights">
                <h2>7. Your rights</h2>
                <p>Under the GDPR you have the right to:</p>
                <ul>
                    <li>access the personal data we hold about you (Art. 15)</li>
                    <li>have incorrect data corrected (Art. 16)</li>
                    <li>have your data deleted (Art. 17) or its processing restricted (Art. 18)</li>
                    <li>receive your data in a portable format (Art. 20)</li>
                    <li>object to processing based on our legitimate interests (Art. 21)</li>
                    <li>withdraw your consent at any time (Art. 7(3))</li>
                </ul>
                <p>You also have the right to lodge a complaint with a data protection supervisory authority, in particular in the EU member state where you live or work.</p>
            </section>

            <section id="contact">
                <h2>8. Contact</h2>
                <p>If you have questions about this policy or want to use your rights, please contact us<?php if ($contactEmail !== ''): ?> at <a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a><?php endif; ?>. We will answer as quickly as possible.</p>
                <p>We may update this policy when our services or legal requirements change. The current version is always available on this page.</p>
            </section>

            <div class="rounded-xl border border-line bg-surface p-5 sm:p-6">
                <p class="!mt-0 font-semibold text-ink">Ready to apply?</p>
                <p class="!mt-1 text-[15px]">Become a Khan Travel B2B partner in a few minutes.</p>
                <a href="<?= e(url('become-a-partner')) ?>#apply" class="btn-primary mt-4 h-11 px-5 !text-white !no-underline">Become a Partner</a>
            </div>
        </article>
    </div>
</main>

<?php require APP_ROOT . '/views/partials/site-footer.php'; ?>
</body>
</html>
