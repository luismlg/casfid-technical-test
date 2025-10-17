<?php

declare(strict_types=1);

use App\Infrastructure\Controller\CreateBookController;
use App\Infrastructure\Controller\DeleteBookController;
use App\Infrastructure\Controller\GetBookController;
use App\Infrastructure\Controller\GetBooksController;
use App\Infrastructure\Controller\UpdateBookController;
use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    // Books endpoints
    $r->addRoute('GET', '/api/books', GetBooksController::class);
    $r->addRoute('GET', '/api/books/{isbn}', GetBookController::class);
    $r->addRoute('POST', '/api/books', CreateBookController::class);
    $r->addRoute('PUT', '/api/books/{isbn}', UpdateBookController::class);
    $r->addRoute('DELETE', '/api/books/{isbn}', DeleteBookController::class);
};
