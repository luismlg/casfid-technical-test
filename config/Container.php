<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Configuración PDO desde variables de entorno
$pdoHost = $_ENV['DB_HOST'] ?? 'db';
$pdoDb = $_ENV['DB_NAME'] ?? 'casfid';
$pdoUser = $_ENV['DB_USER'] ?? 'casfid_user';
$pdoPassword = $_ENV['DB_PASSWORD'] ?? 'casfid_password';
$pdoCharset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
$pdoDsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $pdoHost, $pdoDb, $pdoCharset);

// Construir container
$builder = new ContainerBuilder();

// Definición de PDO
$builder->addDefinitions([
    PDO::class => static function () use ($pdoDsn, $pdoUser, $pdoPassword) {
        $pdo = new PDO(
            $pdoDsn,
            $pdoUser,
            $pdoPassword,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        
        return $pdo;
    },
]);

// Agregar providers
$builder->addDefinitions(require __DIR__ . '/Providers.php');

return $builder->build();

