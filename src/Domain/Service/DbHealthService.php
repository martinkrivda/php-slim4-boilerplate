<?php

declare(strict_types=1);

namespace App\Domain\Service;

final class DbHealthService
{
    public function __construct(private \Closure $pdoFactory)
    {
    }

    public function check(): bool
    {
        try {
            $pdo = ($this->pdoFactory)();
            $stmt = $pdo->query('SELECT 1 AS ok');
            $row = $stmt->fetch();
        } catch (\Throwable $e) {
            return false;
        }

        return is_array($row) && isset($row['ok']);
    }
}
