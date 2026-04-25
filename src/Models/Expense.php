<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Expense
{
    public static function find(int $id): ?array
    {
        $rows = DB::getInstance()->selectWhere('expenses', 'id = ? AND deleted_at IS NULL', [$id]);
        return $rows[0] ?? null;
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('expenses', ['batch_id' => $batchId], $page, $perPage, [
            'extra_where'    => 'deleted_at IS NULL',
            'order_by'       => 'expense_date DESC',
        ]);
    }

    public static function totalForBatch(int $batchId): float
    {
        return DB::getInstance()->sum('expenses', 'amount', 'batch_id = ? AND deleted_at IS NULL', [$batchId]);
    }

    public static function byCategory(int $batchId): array
    {
        $stmt = DB::getInstance()->query(
            'SELECT category, SUM(amount) as total FROM expenses WHERE batch_id = ? AND deleted_at IS NULL GROUP BY category',
            [$batchId]
        );
        return $stmt->fetchAll();
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('expenses', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('expenses', $data, ['id' => $id]);
    }

    public static function softDelete(int $id): int
    {
        return DB::getInstance()->update('expenses', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);
    }
}
