<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Customer
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('customers', ['id' => $id]);
    }

    public static function all(int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('customers', [], $page, $perPage, ['order_by' => 'name ASC']);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('customers', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('customers', $data, ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('customers', ['id' => $id]);
    }
}
