<?php
/**
 * Applicant confirmation: "We Received Your Khan Travel B2B Partner Application".
 *
 * @var array $application
 */

$font = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
$a    = $application;

ob_start();
?>
<p style="margin:0 0 16px 0;">Hello <?= e($a['first_name']) ?>,</p>
<p style="margin:0 0 16px 0;">Thank you for your interest in becoming a Khan Travel B2B partner.</p>
<p style="margin:0 0 16px 0;">We have successfully received your partner application for <strong style="color:#0E1C2B;"><?= e($a['company_name']) ?></strong>.</p>
<p style="margin:0 0 24px 0;">Our team will review your information and contact you regarding the next steps.</p>

<!-- Status box -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F6F8FB; border:1px solid #E3E8EF; border-radius:6px;">
  <tr>
    <td style="padding:18px 20px; font-family:<?= $font ?>;">
      <p style="margin:0 0 6px 0; font-size:12px; line-height:16px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase; color:#5B6B7F;">Application Status</p>
      <span style="display:inline-block; padding:3px 10px; background-color:#FFF7E6; border:1px solid #F5D9A3; border-radius:12px; font-size:13px; line-height:18px; font-weight:bold; color:#9A5B00;">Pending Review</span>
      <p style="margin:12px 0 0 0; font-size:13px; line-height:20px; color:#5B6B7F;">
        Company: <strong style="color:#334155;"><?= e($a['company_name']) ?></strong><br>
        Phone: <?= e($a['phone'] ?? '') ?><br>
        Submitted: <?= e($a['submitted_at']) ?>
      </p>
    </td>
  </tr>
</table>

<!-- Next steps -->
<p style="margin:28px 0 12px 0; font-size:14px; line-height:20px; font-weight:bold; color:#0E1C2B;">What happens next</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<?php foreach ([
    'Our team reviews your application.',
    'We contact you about the next steps.',
    'Once approved, your team can start using the Khan Travel B2B platform.',
] as $n => $step): ?>
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

<p style="margin:20px 0 0 0;">Thank you,<br><strong style="color:#0E1C2B;">Khan Travel Team</strong></p>
<?php
$content = (string) ob_get_clean();

$subject     = 'We Received Your Khan Travel B2B Partner Application';
$preheader   = 'Your application for ' . $a['company_name'] . ' is pending review. We will contact you about the next steps.';
$eyebrow     = 'Partner application';
$heading     = 'Application Received';
$footer_note = 'You received this email because this address was used to apply for a Khan Travel B2B partnership. If this was not you, simply reply and let us know.';

require __DIR__ . '/layout.php';
