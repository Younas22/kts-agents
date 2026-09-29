<?php
/**
 * App folder root → the partner landing page (/become-a-partner).
 * Live, the domain root (/) belongs to Laravel, which sends guests to /become-a-partner.
 */

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

redirect(home_url(), 302);
