<?php

use Dotenv\Dotenv;

// .env dosyasını yükle (vlucas/phpdotenv ile)
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database konfigürasyonunu hazırla
$config = [
    'driver'    => 'mysql',
    'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'port'      => $_ENV['DB_PORT'] ?? '3306',
    'database'  => $_ENV['DB_DATABASE'] ?? 'findikengine',
    'username'  => $_ENV['DB_USERNAME'] ?? 'root',
    'password'  => $_ENV['DB_PASSWORD'] ?? '',
    'charset'   => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
    'prefix'    => '',
    'strict'    => true,
    'engine'    => null,
];

// Core\Database sınıfı ile Eloquent'i başlat
\Core\Database::init($config);
