<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class House
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('houses', ['id' => $id]);
    }

    public static function all(): array
    {
        return DB::getInstance()->select('houses', [], ['order_by' => 'name ASC']);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('houses', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('houses', $data, ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('houses', ['id' => $id]);
    }
}
