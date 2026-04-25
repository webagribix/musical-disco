<?php
declare(strict_types=1);
define('APP_ROOT', __DIR__);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/app.php';

$env  = 'mysql';
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--env=')) $env = substr($arg, 6);
}

require __DIR__ . '/config/database.php';
$cfg = $databases[$env] ?? $databases['mysql'];

$dsn = "mysql:host={$cfg['host']};port={$cfg['port']};charset={$cfg['charset']}";
$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$cfg['database']}`");
$pdo->exec("USE `{$cfg['database']}`");
$pdo->exec("CREATE TABLE IF NOT EXISTS migrations_log (id INT AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(255) NOT NULL UNIQUE, applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

$applied = $pdo->query("SELECT filename FROM migrations_log")->fetchAll(PDO::FETCH_COLUMN);
$files   = glob(__DIR__ . '/migrations/*.sql');
sort($files);

$count = 0;
foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $applied, true)) {
        echo "  skip: $name\n";
        continue;
    }
    $sql = file_get_contents($file);
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if ($stmt !== '') $pdo->exec($stmt . ';');
    }
    $pdo->prepare("INSERT INTO migrations_log (filename) VALUES (?)")->execute([$name]);
    echo "  applied: $name\n";
    $count++;
}
echo "\nDone. Applied $count migration(s).\n";
