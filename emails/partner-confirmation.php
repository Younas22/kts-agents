<?php
/**
 * Applicant confirmation – rendered in the applicant's language (email.confirmation.*).
 *
 * @var array $application
 */

$font      = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
$a         = $application;
$submitted = date((string) t('formats.datetime'), (int) ($a['submitted_ts'] ?? time())) . ' ' . config('app.timezone');
$company   = '<strong style="color:#0E1C2B;">' . e($a['company_name']) . '</strong>';

ob_start();
?>
<p style="margin:0 0 16px 0;"><?= te('email.confirmation.hello', ['name' => $a['first_name']]) ?></p>
<p style="margin:0 0 16px 0;"><?= te('email.confirmation.thanks') ?></p>
<p style="margin:0 0 16px 0;"><?= th('email.confirmation.received', ['company' => $company]) ?></p>
<p style="margin:0 0 24px 0;"><?= te('email.confirmation.review') ?></p>

<!-- Status box -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F6F8FB; border:1px solid #E3E8EF; border-radius:6px;">
  <tr>
    <td style="padding:18px 20px; font-family:<?= $font ?>;">
      <p style="margin:0 0 6px 0; font-size:12px; line-height:16px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase; color:#5B6B7F;"><?= te('email.confirmation.status_label') ?></p>
      <span style="display:inline-block; padding:3px 10px; background-color:#FFF7E6; border:1px solid #F5D9A3; border-radius:12px; font-size:13px; line-height:18px; font-weight:bold; color:#9A5B00;"><?= te('email.confirmation.status') ?></span>
      <p style="margin:12px 0 0 0; font-size:13px; line-height:20px; color:#5B6B7F;">
        <?= te('email.confirmation.company') ?>: <strong style="color:#334155;"><?= e($a['company_name']) ?></strong><br>
        <?= te('email.confirmation.phone') ?>: <?= e($a['phone'] ?? '') ?><br>
        <?= te('email.confirmation.submitted') ?>: <?= e($submitted) ?>
      </p>
    </td>
  </tr>
</table>

<!-- Next steps -->
<p style="margin:28px 0 12px 0; font-size:14px; line-height:20px; font-weight:bold; color:#0E1C2B;"><?= te('email.confirmation.next_title') ?></p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<?php foreach (tl('email.confirmation.next') as $n => $step): ?>
  <tr>
    <td width="34" valign="top" style="width:34px; padding:0 0 10px 0;">
      <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
        <td align="center" valign="middle" width="24" height="24" style="width:24px; height:24px; border-radius:12px; background-color:#EEF6FC; font-family:<?= $font ?>; font-size:12px; line-height:24px; font-weight:bold; color:#0077BE;"><?= $n + 1 ?></td>
      </tr></table>
    </td>
    <td valign="top" style="padding:2px 0 10px 0; font-family:<?= $font ?>; font-size:14px; line-height:20px; color:#334155;"><?= e($step) ?></td>
  </tr>
<?php endforeach; ?>
</table>

<p style="margin:20px 0 0 0;"><?= te('email.confirmation.signoff') ?><br><strong style="color:#0E1C2B;"><?= te('email.confirmation.team') ?></strong></p>
<?php
$content = (string) ob_get_clean();

$subject     = (string) t('email.confirmation.subject');
$preheader   = (string) t('email.confirmation.preheader', ['company' => $a['company_name']]);
$eyebrow     = (string) t('email.confirmation.eyebrow');
$heading     = (string) t('email.confirmation.heading');
$footer_note = (string) t('email.confirmation.footer_note');

require __DIR__ . '/layout.php';
