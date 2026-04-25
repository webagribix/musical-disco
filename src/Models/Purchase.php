<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Purchase
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('purchases', ['id' => $id]);
    }

    public static function all(int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('purchases', [], $page, $perPage, ['order_by' => 'purchase_date DESC']);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('purchases', $data);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('purchases', ['id' => $id]);
    }
}
