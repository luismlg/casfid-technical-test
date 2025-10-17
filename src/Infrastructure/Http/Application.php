<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Application
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly Dispatcher $dispatcher
    ) {
    }

    public function handle(Request $request): Response
    {
        // Manejar CORS preflight
        if ($request->getMethod() === 'OPTIONS') {
            return $this->createCorsResponse(new JsonResponse(null, 204));
        }

        // Dispatch de la ruta
        $routeInfo = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getPathInfo()
        );

        $response = match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => new JsonResponse(['error' => 'Endpoint not found'], 404),
            Dispatcher::METHOD_NOT_ALLOWED => new JsonResponse([
                'error' => 'Method not allowed',
                'allowed_methods' => $routeInfo[1]
            ], 405),
            Dispatcher::FOUND => $this->handleFoundRoute($request, $routeInfo[1], $routeInfo[2]),
            default => new JsonResponse(['error' => 'Internal server error'], 500),
        };

        return $this->createCorsResponse($response);
    }

    private function handleFoundRoute(Request $request, string $handler, array $vars): Response
    {
        // Agregar variables de ruta al request
        foreach ($vars as $key => $value) {
            $request->attributes->set($key, $value);
        }

        // Resolver y ejecutar el controlador
        $controller = $this->container->get($handler);

        return $controller($request);
    }

    private function createCorsResponse(Response $response): Response
    {
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        return $response;
    }
}
