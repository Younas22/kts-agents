<?php
/**
 * PDO connection to the existing TravelBookingPanel database.
 */

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            config('db.host'),
            config('db.port'),
            config('db.database')
        );

        $pdo = new PDO($dsn, (string) config('db.username'), (string) config('db.password'), [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5,
        ]);
    }

    return $pdo;
}

/** True when a PDOException is a MySQL duplicate-key violation (error 1062). */
function is_duplicate_key_error(PDOException $e, ?string $indexName = null): bool
{
    $isDuplicate = ($e->errorInfo[1] ?? null) === 1062;
    return $isDuplicate && ($indexName === null || str_contains($e->getMessage(), $indexName));
}
