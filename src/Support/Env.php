<?php

declare(strict_types=1);

namespace App\Support;

final class Env
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }

    public static function required(string $key): string
    {
        $value = getenv($key);
        if ($value === false || $value === '') {
            throw new \RuntimeException('Missing required env var: ' . $key);
        }

        return $value;
    }
}
