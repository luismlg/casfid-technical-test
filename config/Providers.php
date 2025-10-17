<?php

declare(strict_types=1);

use App\Application\Command\CreateBook\CreateBook;
use App\Application\Command\UpdateBook\UpdateBook;
use App\Application\Command\DeleteBook\DeleteBook;
use App\Application\Listener\BookModifiedListener;
use App\Application\Query\GetBook\GetBook;
use App\Application\Query\GetBooks\GetBooks;
use App\Domain\Book\BookRepositoryInterface;
use App\Domain\Book\Event\BookModified;
use App\Domain\Book\Service\BookDescriptionProviderInterface;
use App\Infrastructure\Controller\CreateBookController;
use App\Infrastructure\Controller\GetBookController;
use App\Infrastructure\Controller\GetBooksController;
use App\Infrastructure\Controller\UpdateBookController;
use App\Infrastructure\Controller\DeleteBookController;
use App\Infrastructure\Persistence\PdoBookRepository;
use App\Infrastructure\Service\OpenLibraryBookDescriptionProvider;
use DI\Container;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

return [
    /** Logger */
    LoggerInterface::class => static function () {
        $logLevel = $_ENV['LOG_LEVEL'] ?? 'DEBUG';
        $logPath = $_ENV['LOG_PATH'] ?? __DIR__ . '/../var/logs/api.log';
        
        // Crear directorio si no existe
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logger = new Logger('api');
        
        // Convertir string a Level enum (Monolog 3.x)
        $level = match(strtoupper($logLevel)) {
            'DEBUG' => Level::Debug,
            'INFO' => Level::Info,
            'WARNING' => Level::Warning,
            'ERROR' => Level::Error,
            default => Level::Debug,
        };
        
        $logger->pushHandler(new StreamHandler($logPath, $level));
        
        return $logger;
    },

    /** Repositories */
    BookRepositoryInterface::class => static function (Container $c) {
        return new PdoBookRepository($c->get(PDO::class));
    },

    /** Services */
    BookDescriptionProviderInterface::class => static fn(Container $c) => new OpenLibraryBookDescriptionProvider(
        new Client([
            'timeout' => 5,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]),
        $c->get(LoggerInterface::class)
    ),

    /** Event Dispatcher con listener */
    EventDispatcherInterface::class => static function (Container $c) {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(
            BookModified::class,
            [new BookModifiedListener($c->get(LoggerInterface::class)), '__invoke']
        );
        return $dispatcher;
    },
    EventDispatcher::class => DI\get(EventDispatcherInterface::class),

    /** Use Cases (Handlers) */
    CreateBook::class => DI\autowire(),
    UpdateBook::class => DI\autowire(),
    DeleteBook::class => DI\autowire(),
    GetBook::class => DI\autowire(),
    GetBooks::class => DI\autowire(),

    /** Controllers */
    CreateBookController::class => DI\autowire(),
    GetBookController::class => DI\autowire(),
    GetBooksController::class => DI\autowire(),
    UpdateBookController::class => DI\autowire(),
    DeleteBookController::class => DI\autowire(),
];
