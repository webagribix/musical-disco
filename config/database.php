<?php
return [
    'default' => $_ENV['DB_CONNECTION'] ?? 'mysql',
    'connections' => [
        'mysql' => [
            'driver'   => 'mysql',
            'host'     => $_ENV['DB_HOST']     ?? '127.0.0.1',
            'port'     => $_ENV['DB_PORT']     ?? '3306',
            'dbname'   => $_ENV['DB_DATABASE'] ?? 'poultry_os',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset'  => 'utf8mb4',
            'options'  => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ],
        ],
        'test' => [
            'driver'   => 'mysql',
            'host'     => $_ENV['TEST_DB_HOST']     ?? '127.0.0.1',
            'port'     => $_ENV['TEST_DB_PORT']     ?? '3306',
            'dbname'   => $_ENV['TEST_DB_DATABASE'] ?? 'poultry_os_test',
            'username' => $_ENV['TEST_DB_USERNAME'] ?? 'poultry',
            'password' => $_ENV['TEST_DB_PASSWORD'] ?? 'secret',
            'charset'  => 'utf8mb4',
            'options'  => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ],
        ],
    ],
];
