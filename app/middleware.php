<?php

declare(strict_types=1);

use App\Application\Http\RequestIdMiddleware;
use App\Application\Http\RequestLoggingMiddleware;
use Psr\Container\ContainerInterface;
use Slim\App;

return static function (App $app): void {
    $app->add(RequestIdMiddleware::class);
    $app->add(RequestLoggingMiddleware::class);
    $app->addRoutingMiddleware();

    $container = $app->getContainer();
    $settings = $container instanceof ContainerInterface ? $container->get('settings') : [
        'displayErrorDetails' => false,
        'logErrors' => true,
        'logErrorDetails' => true,
    ];

    $app->addErrorMiddleware(
        $settings['displayErrorDetails'],
        $settings['logErrors'],
        $settings['logErrorDetails']
    );
};
