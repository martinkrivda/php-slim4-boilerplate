<?php

declare(strict_types=1);

use App\Support\Env;

return [
    'displayErrorDetails' => Env::get('APP_DEBUG', '0') === '1',
    'logErrors' => true,
    'logErrorDetails' => true,
    'logger' => [
        'name' => 'app',
        'path' => __DIR__ . '/../var/logs/app.log',
        'level' => Env::get('APP_LOG_LEVEL', 'info'),
        'maxFiles' => (int) Env::get('APP_LOG_MAX_FILES', '7'),
    ],
    'view' => [
        'path' => __DIR__ . '/../templates',
        'cache' => Env::get('APP_ENV', 'local') === 'prod'
            ? __DIR__ . '/../var/cache/twig'
            : false,
    ],
];
