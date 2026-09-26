<?php
/**
 * Shared email layout (table based, inline styles – Gmail, Outlook, Apple Mail, mobile).
 *
 * @var string $subject
 * @var string $preheader
 * @var string $eyebrow
 * @var string $heading
 * @var string $content   pre-rendered, escaped HTML
 * @var string $footer_note
 * @var string $logo_url
 * @var string $main_site
 * @var string $year
 */

$font = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="<?= e(current_language()) ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="x-apple-disable-message-reformatting">
<meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title><?= e($subject) ?></title>
<!--[if mso]>
<noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
<style>table,td,p,a,span,h1{font-family:Arial,sans-serif !important;}</style>
<![endif]-->
<style type="text/css">
  body { margin:0 !important; padding:0 !important; width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
  table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
  img { border:0; outline:none; text-decoration:none; -ms-interpolation-mode:bicubic; display:block; }
  a[x-apple-data-detectors] { color:inherit !important; text-decoration:none !important; }
  u + #body a { color:inherit; text-decoration:none; }
  @media only screen and (max-width: 620px) {
    .container { width:100% !important; }
    .px { padding-left:24px !important; padding-right:24px !important; }
    .h1 { font-size:22px !important; line-height:30px !important; }
    .row-label, .row-value { display:block !important; width:100% !important; box-sizing:border-box; }
    .row-label { padding-bottom:2px !important; border-bottom:0 !important; }
    .row-value { padding-top:0 !important; }
    .btn a { display:block !important; }
  }
</style>
</head>
<body id="body" style="margin:0; padding:0; background-color:#FFFFFF; word-spacing:normal;">
<div style="display:none; font-size:1px; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden; mso-hide:all;"><?= e($preheader) ?>&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#FFFFFF;">
<tr>
<td align="center" style="padding:32px 12px;">
  <!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" align="center"><tr><td><![endif]-->
  <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px; background-color:#FFFFFF; border:1px solid #E3E8EF; border-radius:8px;">
    <!-- Blue accent bar -->
    <tr><td height="5" style="height:5px; line-height:5px; font-size:5px; background-color:#0077BE; border-radius:8px 8px 0 0;">&nbsp;</td></tr>

    <!-- Logo -->
    <tr>
      <td class="px" style="padding:28px 40px 24px 40px; border-bottom:1px solid #EEF1F5;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td align="left" valign="middle">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>
<?php if ($logo_url !== ''): ?>
                  <td valign="middle" width="56" style="width:56px; padding-right:12px;">
                    <img src="<?= e($logo_url) ?>" width="56" height="56" alt="Khan Travel Services e.K." style="width:56px; height:56px; color:#0077BE; font-family:<?= $font ?>; font-size:12px; font-weight:bold;">
                  </td>
<?php endif; ?>
                  <td valign="middle" style="font-family:<?= $font ?>;">
                    <span style="display:block; font-size:18px; line-height:22px; font-weight:bold; color:#0E1C2B;">Khan Travel Services e.K.</span>
                    <span style="display:block; font-size:12px; line-height:16px; color:#5B6B7F;"><?= te('email.program') ?></span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- Heading -->
    <tr>
      <td class="px" style="padding:32px 40px 8px 40px; font-family:<?= $font ?>;">
        <p style="margin:0 0 8px 0; font-size:12px; line-height:16px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#0077BE;"><?= e($eyebrow) ?></p>
        <h1 class="h1" style="margin:0; font-size:26px; line-height:34px; font-weight:bold; color:#0E1C2B;"><?= e($heading) ?></h1>
      </td>
    </tr>

    <!-- Content -->
    <tr>
      <td class="px" style="padding:16px 40px 36px 40px; font-family:<?= $font ?>; font-size:15px; line-height:24px; color:#334155;">
        <?= $content ?>
      </td>
    </tr>

    <!-- Footer -->
    <tr>
      <td class="px" style="padding:24px 40px 28px 40px; background-color:#F6F8FB; border-top:1px solid #E3E8EF; border-radius:0 0 8px 8px; font-family:<?= $font ?>; font-size:12px; line-height:19px; color:#5B6B7F;">
        <p style="margin:0 0 6px 0; font-weight:bold; color:#334155;">Khan Travel Services e.K. &middot; <?= te('email.program') ?></p>
<?php if ($main_site !== ''): ?>
        <p style="margin:0 0 6px 0;"><a href="<?= e($main_site) ?>" style="color:#0077BE; text-decoration:none;"><?= e(preg_replace('#^https?://#', '', $main_site)) ?></a></p>
<?php endif; ?>
        <p style="margin:0;"><?= e($footer_note) ?></p>
        <p style="margin:12px 0 0 0; color:#8593A3;"><?= te('email.copyright', ['year' => $year]) ?></p>
      </td>
    </tr>
  </table>
  <!--[if mso]></td></tr></table><![endif]-->
</td>
</tr>
</table>
</body>
</html>
