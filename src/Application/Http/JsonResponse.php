<?php

declare(strict_types=1);

namespace App\Application\Http;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class JsonResponse
{
    public static function success(Request $request, Response $response, array $data, array $meta = []): Response
    {
        $payload = [
            'success' => true,
            'data' => $data,
            'error' => null,
            'meta' => self::buildMeta($request, $meta),
        ];

        return self::write($response, $payload, 200);
    }

    public static function error(
        Request $request,
        Response $response,
        array $error,
        int $status
    ): Response {
        $payload = [
            'success' => false,
            'data' => null,
            'error' => self::normalizeError($request, $error, $status),
            'meta' => self::buildMeta($request),
        ];

        return self::write($response, $payload, $status);
    }

    public static function validationError(
        Request $request,
        Response $response,
        array $error
    ): Response {
        return self::error($request, $response, $error, 422);
    }

    private static function write(Response $response, array $payload, int $status): Response
    {
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $response->getBody()->write($json === false ? '{}' : $json);

        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

    private static function buildMeta(Request $request, array $meta = []): array
    {
        $requestId = $request->getAttribute('requestId');
        if (!is_string($requestId) || $requestId === '') {
            $requestId = bin2hex(random_bytes(16));
        }

        return array_merge([
            'requestId' => $requestId,
            'timestamp' => gmdate('c'),
        ], $meta);
    }

    private static function normalizeError(Request $request, array $error, int $status): array
    {
        $instance = $request->getUri()->getPath();
        $normalized = $error;
        $normalized['status'] = $normalized['status'] ?? $status;
        $normalized['instance'] = $normalized['instance'] ?? $instance;
        $normalized['errors'] = $normalized['errors'] ?? [];

        return $normalized;
    }
}
