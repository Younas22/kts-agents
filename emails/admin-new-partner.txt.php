<?php
/** Plain-text version of the admin notification. @var array $application @var string $review_url */
$a = $application;
?>
New Partner Application

A travel agency has applied to become a Khan Travel Services e.K. B2B partner.

Company: <?= $a['company_name'] ?>

First Name: <?= $a['first_name'] ?>

Last Name: <?= $a['last_name'] ?>

Email: <?= $a['email'] ?>

Phone: <?= $a['phone'] ?? '' ?>

Previous Contact: <?= $a['previous_contact_label'] ?>

Language: <?= languages_config()['languages'][$a['language'] ?? '']['name'] ?? ($a['language'] ?? '') ?>

Application Status: Pending Review
Submitted At: <?= $a['submitted_at'] ?>


Saved as user #<?= (int) $a['id'] ?> (agent code <?= $a['agent_code'] ?>, approval status: pending, status: inactive).

<?php if ($review_url !== ''): ?>
Review Application: <?= $review_url ?>
<?php else: ?>
Review Application: open the admin panel → Agents → Pending.
<?php endif; ?>

--
Khan Travel Services e.K. · B2B Partner Program
