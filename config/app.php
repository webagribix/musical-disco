<?php
define('APP_NAME', 'PoultryOS Kenya');
define('APP_VERSION', '1.0.0');
define('APP_CURRENCY', 'KES');
define('APP_LOCALE', $_ENV['APP_LOCALE'] ?? 'en');
define('BASE_URL', $_ENV['APP_URL'] ?? 'http://localhost');
define('BASE_PATH', dirname(__DIR__));

define('BROODING_TEMP_MIN', 32);
define('BROODING_TEMP_MAX', 35);
define('AMBIENT_TEMP_MAX', 30);
define('MORTALITY_ALERT_THRESHOLD', 0.02);
define('WATER_DROP_ALERT_THRESHOLD', 0.30);
define('FEED_DROP_ALERT_THRESHOLD', 0.30);
define('EGG_DROP_ALERT_THRESHOLD', 0.20);

define('ITEMS_PER_PAGE', 20);
define('API_VERSION', 'v1');

$dotenvFile = BASE_PATH . '/.env';
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key]    = $value;
            putenv("$key=$value");
        }
    }
}
