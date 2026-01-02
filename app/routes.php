<?php

declare(strict_types=1);

use App\Application\Action\DbCheckAction;
use App\Application\Action\HealthAction;
use App\Application\Action\HomeAction;
use Slim\App;

return static function (App $app): void {
    $app->get('/', HomeAction::class);
    $app->get('/health', HealthAction::class);
    $app->get('/health.php', HealthAction::class);
    $app->get('/db-check', DbCheckAction::class);
    $app->get('/db-check.php', DbCheckAction::class);
};
