<?php
declare(strict_types=1);

namespace App\Core;

class Auth
{
    private static ?array $currentUser = null;

    public static function login(string $email, string $password): bool
    {
        $db   = DB::getInstance();
        $user = $db->selectOne('users', ['email' => $email, 'is_active' => 1]);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }
        if ($user['deleted_at'] !== null) {
            return false;
        }

        // Rotate API token on login
        $token = bin2hex(random_bytes(40));
        $db->update('users', ['api_token' => $token, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $user['id']]);
        $user['api_token'] = $token;

        Session::start();
        Session::regenerate();
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('api_token', $token);

        static::$currentUser = $user;
        return true;
    }

    public static function logout(): void
    {
        $user = static::user();
        if ($user) {
            DB::getInstance()->update('users', ['api_token' => null], ['id' => $user['id']]);
        }
        Session::destroy();
        static::$currentUser = null;
    }

    public static function user(): ?array
    {
        if (static::$currentUser !== null) {
            return static::$currentUser;
        }

        Session::start();
        $userId = Session::get('user_id');
        if (!$userId) {
            return null;
        }

        $user = DB::getInstance()->selectOne('users', ['id' => $userId, 'is_active' => 1]);
        if (!$user || $user['deleted_at'] !== null) {
            return null;
        }

        static::$currentUser = $user;
        return $user;
    }

    public static function tokenUser(string $token): ?array
    {
        if (empty($token)) return null;

        $user = DB::getInstance()->selectOne('users', ['api_token' => $token, 'is_active' => 1]);
        if (!$user || $user['deleted_at'] !== null) {
            return null;
        }
        return $user;
    }

    public static function check(): bool
    {
        return static::user() !== null;
    }

    public static function hasRole(string|array $roles): bool
    {
        $user = static::user();
        if (!$user) return false;

        $allowed = is_array($roles) ? $roles : [$roles];
        return in_array($user['role'], $allowed, true);
    }

    public static function isOwner(): bool
    {
        return static::hasRole('owner');
    }

    public static function isManager(): bool
    {
        return static::hasRole(['owner', 'manager']);
    }

    public static function isWorker(): bool
    {
        return static::check();
    }

    public static function id(): ?int
    {
        return static::user() ? (int) static::user()['id'] : null;
    }

    public static function setCurrentUser(array $user): void
    {
        static::$currentUser = $user;
    }

    public static function clearCurrentUser(): void
    {
        static::$currentUser = null;
    }
}
