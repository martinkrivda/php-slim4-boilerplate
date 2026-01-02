<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Http\JsonResponse;
use App\Domain\Service\DbHealthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class DbCheckAction
{
    public function __construct(private DbHealthService $service)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        if (!$this->service->check()) {
            return JsonResponse::error(
                $request,
                $response,
                [
                    'type' => 'https://example.com/problems/internal-server-error',
                    'title' => 'Internal server error',
                    'detail' => 'DB check failed.',
                    'code' => 'DB_CHECK_FAILED',
                    'errors' => [],
                ],
                500
            );
        }

        return JsonResponse::success($request, $response, [
            'status' => 'ok',
        ]);
    }
}
