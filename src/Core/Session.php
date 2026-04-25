<?php
declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (!static::$started && session_status() === PHP_SESSION_NONE) {
            session_name('poultry_os');
            session_set_cookie_params([
                'lifetime' => 86400 * 7,
                'path'     => '/',
                'secure'   => isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
            static::$started = true;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        static::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        static::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        static::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        static::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        static::start();
        $_SESSION = [];
        session_destroy();
        static::$started = false;
    }

    public static function flash(string $key, mixed $value): void
    {
        static::set('_flash_' . $key, $value);
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = static::get('_flash_' . $key, $default);
        static::remove('_flash_' . $key);
        return $value;
    }

    public static function regenerate(): void
    {
        static::start();
        session_regenerate_id(true);
    }
}
