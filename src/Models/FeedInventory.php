<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class FeedInventory
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('feed_inventory', ['id' => $id]);
    }

    public static function all(int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('feed_inventory', [], $page, $perPage, [
            'order_by' => 'created_at DESC',
        ]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('feed_inventory', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('feed_inventory', $data, ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('feed_inventory', ['id' => $id]);
    }

    public static function deduct(int $id, float $qty): void
    {
        DB::getInstance()->query(
            'UPDATE feed_inventory SET quantity_kg = quantity_kg - ?, updated_at = NOW() WHERE id = ?',
            [$qty, $id]
        );
    }
}
