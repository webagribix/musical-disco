<?php
declare(strict_types=1);

namespace App\Core;

class View
{
    private static array $shared = [];

    public static function share(string $key, mixed $value): void
    {
        static::$shared[$key] = $value;
    }

    public static function render(string $template, array $data = []): void
    {
        $data = array_merge(static::$shared, $data);
        $file = BASE_PATH . '/views/' . ltrim($template, '/') . '.php';

        if (!file_exists($file)) {
            throw new \RuntimeException("View not found: $file");
        }

        extract($data, EXTR_SKIP);
        require $file;
    }

    public static function make(string $template, array $data = []): string
    {
        ob_start();
        static::render($template, $data);
        return ob_get_clean();
    }

    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function currency(float $amount, string $currency = APP_CURRENCY): string
    {
        return $currency . ' ' . number_format($amount, 2);
    }

    public static function partial(string $template, array $data = []): void
    {
        static::render($template, $data);
    }
}
