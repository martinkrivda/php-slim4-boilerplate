<?php

declare(strict_types=1);

use App\Application\Http\RequestLoggingMiddleware;
use App\Application\Http\RequestIdMiddleware;
use App\Infrastructure\Persistence\Pdo;
use App\Domain\Service\DbHealthService;
use App\Infrastructure\Logging\GzipRotatingFileHandler;
use DI\ContainerBuilder;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Views\Twig;

$builder = new ContainerBuilder();

$builder->addDefinitions([
    'settings' => require __DIR__ . '/../config/settings.php',
    \PDO::class => static function (): \PDO {
        return Pdo::connect();
    },
    Twig::class => static function (ContainerInterface $container): Twig {
        $settings = $container->get('settings');

        return Twig::create($settings['view']['path'], [
            'cache' => $settings['view']['cache'],
        ]);
    },
    LoggerInterface::class => static function (ContainerInterface $container): LoggerInterface {
        $settings = $container->get('settings');
        $logPath = $settings['logger']['path'];
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        $logger = new Logger($settings['logger']['name']);
        $logger->pushHandler(new GzipRotatingFileHandler(
            $logPath,
            $settings['logger']['maxFiles'],
            $settings['logger']['level']
        ));

        return $logger;
    },
    RequestLoggingMiddleware::class => static function (ContainerInterface $container): RequestLoggingMiddleware {
        return new RequestLoggingMiddleware($container->get(LoggerInterface::class));
    },
    RequestIdMiddleware::class => static function (): RequestIdMiddleware {
        return new RequestIdMiddleware();
    },
    DbHealthService::class => static function (): DbHealthService {
        return new DbHealthService(static fn (): \PDO => Pdo::connect());
    },
]);

return $builder->build();
