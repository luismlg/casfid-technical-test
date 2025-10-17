<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Http\Application;
use App\Infrastructure\Service\JwtService;
use FastRoute\RouteCollector;
use Symfony\Component\HttpFoundation\Request;

// Cargar container (incluye dotenv y configuración)
$container = require __DIR__ . '/../config/Container.php';

// Inicializar JwtService
$jwtSecret = $_ENV['JWT_SECRET'] ?? '';
$jwtEnabled = filter_var($_ENV['JWT_ENABLED'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
JwtService::initialize($jwtSecret, $jwtEnabled);

// Configurar dispatcher con las rutas
$dispatcher = FastRoute\simpleDispatcher(require __DIR__ . '/../config/routes.php');

// Crear aplicación
$app = new Application($container, $dispatcher);

// Manejar request y enviar respuesta
$request = Request::createFromGlobals();
$response = $app->handle($request);
$response->send();

