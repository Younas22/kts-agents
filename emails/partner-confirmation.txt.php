<?php
/** Plain-text version of the applicant confirmation (applicant's language). @var array $application */
$a = $application;
?>
<?= t('email.confirmation.hello', ['name' => $a['first_name']]) ?>


<?= t('email.confirmation.thanks') ?>


<?= t('email.confirmation.received', ['company' => $a['company_name']]) ?>


<?= t('email.confirmation.review') ?>


<?= t('email.confirmation.status_label') ?>: <?= t('email.confirmation.status') ?>


<?= t('email.confirmation.signoff') ?>

<?= t('email.confirmation.team') ?>


--
Khan Travel Services e.K. · <?= t('email.program') ?>
