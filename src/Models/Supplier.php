<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Supplier
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('suppliers', ['id' => $id]);
    }

    public static function all(int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('suppliers', [], $page, $perPage, ['order_by' => 'name ASC']);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('suppliers', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('suppliers', $data, ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('suppliers', ['id' => $id]);
    }
}
