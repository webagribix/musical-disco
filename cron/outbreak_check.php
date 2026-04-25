<?php
declare(strict_types=1);
chdir(dirname(__DIR__));
require_once 'vendor/autoload.php';
require_once 'config/app.php';
(new App\Jobs\OutbreakCheckJob())->handle();
echo "[" . date('Y-m-d H:i:s') . "] Outbreak check completed.\n";
