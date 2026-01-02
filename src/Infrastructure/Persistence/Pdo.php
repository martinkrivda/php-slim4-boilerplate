<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Support\Env;

final class Pdo
{
    public static function connect(): \PDO
    {
        $host = Env::required('DB_HOST');
        $user = Env::required('DB_USER');
        $pass = Env::required('DB_PASS');
        $name = Env::required('DB_NAME');
        $port = (int) Env::get('DB_PORT', '3306');
        $charset = Env::get('DB_CHARSET', 'utf8mb4');

        $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $name . ';charset=' . $charset;

        try {
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\Throwable $e) {
            throw new \RuntimeException('DB connection failed.', 0, $e);
        }

        return $pdo;
    }
}
