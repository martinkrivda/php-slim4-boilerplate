<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Http\JsonResponse;
use App\Support\Env;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class HealthAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        return JsonResponse::success($request, $response, [
            'checks' => [],
            'service' => Env::get('APP_SERVICE_NAME', 'php-slim4-boilerplate'),
            'status' => 'healthy',
            'version' => Env::get('APP_VERSION', '0.1.0'),
        ]);
    }
}
