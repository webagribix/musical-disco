<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class User
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('users', ['id' => $id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return DB::getInstance()->selectOne('users', ['email' => $email]);
    }

    public static function create(array $data): int|string
    {
        $data['password']   = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('users', $data);
    }

    public static function update(int $id, array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('users', $data, ['id' => $id]);
    }

    public static function softDelete(int $id): int
    {
        return DB::getInstance()->update('users', [
            'deleted_at' => date('Y-m-d H:i:s'),
            'is_active'  => 0,
        ], ['id' => $id]);
    }

    public static function all(int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate(
            'users',
            [],
            $page,
            $perPage,
            [
                'extra_where'    => 'deleted_at IS NULL',
                'order_by'       => 'created_at DESC',
            ]
        );
    }

    public static function sanitize(array $user): array
    {
        unset($user['password'], $user['api_token']);
        return $user;
    }
}
