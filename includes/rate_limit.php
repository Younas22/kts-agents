<?php
/**
 * Small file-based rate limiter (per IP address) for form submissions.
 */

declare(strict_types=1);

/**
 * Record an attempt for $key and report whether it is still within the limit.
 */
function rate_limit_hit(string $key, int $maxAttempts, int $windowSeconds): bool
{
    $dir = STORAGE_PATH . '/cache/ratelimit';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $file   = $dir . '/' . hash('sha256', $key) . '.json';
    $handle = @fopen($file, 'c+');
    if ($handle === false) {
        log_warning('Rate limiter storage not writable', ['dir' => $dir]);
        return true; // fail open – never block real users because of a storage issue
    }

    try {
        flock($handle, LOCK_EX);

        $now      = time();
        $attempts = json_decode((string) stream_get_contents($handle), true);
        $attempts = array_values(array_filter(
            is_array($attempts) ? $attempts : [],
            static fn ($t) => is_int($t) && $t > $now - $windowSeconds
        ));

        $allowed = count($attempts) < $maxAttempts;
        if ($allowed) {
            $attempts[] = $now;
        }

        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode($attempts));
        fflush($handle);

        return $allowed;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
