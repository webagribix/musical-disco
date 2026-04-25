<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Rule
{
    public static function all(): array
    {
        return DB::getInstance()->select('rules', ['is_active' => 1]);
    }

    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('rules', ['id' => $id]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('rules', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('rules', $data, ['id' => $id]);
    }
}
