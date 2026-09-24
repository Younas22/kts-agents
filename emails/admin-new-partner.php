<?php
/**
 * Admin notification: "New Khan Travel B2B Partner Application".
 *
 * @var array  $application
 * @var string $review_url  empty when ADMIN_REVIEW_URL is not configured
 */

$font = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
$a    = $application;

$phone      = (string) ($a['phone'] ?? '');
$phoneTel   = preg_replace('/[^0-9+]/', '', $phone) ?? '';
// wa.me needs the full international number, so only offer it when a country code was given.
$whatsappNo = str_starts_with($phoneTel, '+') ? ltrim($phoneTel, '+') : '';

$rows = [
    'Company'          => e($a['company_name']),
    'First Name'       => e($a['first_name']),
    'Last Name'        => e($a['last_name']),
    'Email'            => '<a href="mailto:' . e($a['email']) . '" style="color:#0077BE; text-decoration:none;">' . e($a['email']) . '</a>',
    'Phone'            => $phone !== ''
        ? '<a href="tel:' . e($phoneTel) . '" style="color:#0077BE; text-decoration:none;">' . e($phone) . '</a>'
        : '—',
    'Previous Contact' => e($a['previous_contact_label']),
    'Application Status' => '<span style="display:inline-block; padding:3px 10px; background-color:#FFF7E6; border:1px solid #F5D9A3; border-radius:12px; font-size:12px; line-height:18px; font-weight:bold; color:#9A5B00;">Pending Review</span>',
    'Submitted At'     => e($a['submitted_at']),
];

ob_start();
?>
<p style="margin:0 0 24px 0;">A travel agency has applied to become a Khan Travel B2B partner. The application is waiting for your review.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #E3E8EF; border-radius:6px;">
<?php $i = 0; foreach ($rows as $label => $value): $border = $i++ === 0 ? '' : 'border-top:1px solid #EEF1F5;'; ?>
  <tr>
    <td class="row-label" width="170" valign="top" style="width:170px; padding:12px 16px; <?= $border ?> background-color:#F9FAFC; font-family:<?= $font ?>; font-size:13px; line-height:20px; font-weight:bold; color:#5B6B7F;"><?= e($label) ?></td>
    <td class="row-value" valign="top" style="padding:12px 16px; <?= $border ?> font-family:<?= $font ?>; font-size:14px; line-height:20px; color:#0E1C2B; word-break:break-word;"><?= $value ?></td>
  </tr>
<?php endforeach; ?>
</table>

<!-- Quick contact -->
<p style="margin:24px 0 10px 0; font-size:14px; line-height:20px; font-weight:bold; color:#0E1C2B;">Contact the applicant</p>
<table role="presentation" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <?php
    $actions = [['Email ' . $a['first_name'], 'mailto:' . $a['email']]];
    if ($phoneTel !== '') {
        $actions[] = ['Call', 'tel:' . $phoneTel];
    }
    if ($whatsappNo !== '') {
        $actions[] = ['WhatsApp', 'https://wa.me/' . $whatsappNo];
    }
    foreach ($actions as [$label, $href]): ?>
    <td style="padding:0 8px 8px 0;">
      <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
        <td align="center" style="border:1px solid #AFD4EE; border-radius:6px; background-color:#EEF6FC;">
          <a href="<?= e($href) ?>" target="_blank" style="display:inline-block; padding:9px 16px; font-family:<?= $font ?>; font-size:14px; line-height:18px; font-weight:bold; color:#005A91; text-decoration:none;"><?= e($label) ?></a>
        </td>
      </tr></table>
    </td>
    <?php endforeach; ?>
  </tr>
</table>

<p style="margin:16px 0 0 0; font-size:13px; line-height:20px; color:#5B6B7F;">
  Saved in the users table as user #<?= (int) $a['id'] ?> with agent code <strong style="color:#334155;"><?= e($a['agent_code']) ?></strong>
  (approval status <em>pending</em>, account status <em>inactive</em>).
</p>

<?php if ($review_url !== ''): ?>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" class="btn" style="margin:28px 0 0 0;">
  <tr>
    <td align="center" bgcolor="#0077BE" style="border-radius:6px; background-color:#0077BE;">
      <a href="<?= e($review_url) ?>" target="_blank" style="display:inline-block; padding:13px 28px; font-family:<?= $font ?>; font-size:15px; line-height:20px; font-weight:bold; color:#FFFFFF; text-decoration:none; border-radius:6px;">Review Application</a>
    </td>
  </tr>
</table>
<?php else: ?>
<p style="margin:24px 0 0 0; padding:14px 16px; background-color:#EEF6FC; border-left:3px solid #0077BE; font-size:14px; line-height:22px; color:#084A75;">
  <strong>Review Application:</strong> open the admin panel and go to <strong>Agents &rarr; Pending</strong> to approve or decline this application.
</p>
<?php endif; ?>

<p style="margin:24px 0 0 0; font-size:13px; line-height:20px; color:#5B6B7F;">Reply to this email to contact the applicant directly.</p>
<?php
$content = (string) ob_get_clean();

$subject     = 'New Khan Travel B2B Partner Application';
$preheader   = $a['company_name'] . ' applied to become a B2B partner – pending review.';
$eyebrow     = 'Admin notification';
$heading     = 'New Partner Application';
$footer_note = 'You are receiving this because this address is set as ADMIN_EMAIL for the partner registration page.';

require __DIR__ . '/layout.php';
