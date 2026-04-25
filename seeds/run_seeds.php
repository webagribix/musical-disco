<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/app.php';

echo "Running seeders...\n";
require_once __DIR__ . '/BreedSeeder.php';
require_once __DIR__ . '/RulesSeeder.php';

$db = App\Core\DB::getInstance();
(new BreedSeeder($db))->run();
(new RulesSeeder($db))->run();
echo "Done.\n";
