<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';
require APP_ROOT . '/config/app.php';

$app = new \App\Core\App();
$app->run();
