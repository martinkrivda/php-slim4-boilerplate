<?php

declare(strict_types=1);

namespace App\Application\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class HomeAction
{
    public function __construct(private Twig $twig)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'home.twig', [
            'title' => 'PHP Slim 4 Boilerplate',
            'tagline' => 'Slim 4 front controller, Nginx + PHP-FPM, Docker single image.',
        ]);
    }
}
