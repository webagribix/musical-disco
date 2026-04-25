<?php
declare(strict_types=1);
define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/config/app.php';

// Point DB to test database
putenv('DB_DATABASE=poultry_farm_test');
