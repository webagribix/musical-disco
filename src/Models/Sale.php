<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Sale
{
    public static function find(int $id): ?array
    {
        $rows = DB::getInstance()->selectWhere('sales', 'id = ? AND deleted_at IS NULL', [$id]);
        return $rows[0] ?? null;
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('sales', ['batch_id' => $batchId], $page, $perPage, [
            'extra_where' => 'deleted_at IS NULL',
            'order_by'    => 'sale_date DESC',
        ]);
    }

    public static function totalRevenueForBatch(int $batchId): float
    {
        return DB::getInstance()->sum('sales', 'total_amount', 'batch_id = ? AND deleted_at IS NULL', [$batchId]);
    }

    public static function soldBirdsForBatch(int $batchId): int
    {
        $stmt = DB::getInstance()->query(
            "SELECT COALESCE(SUM(quantity),0) FROM sales WHERE batch_id = ? AND sale_type IN ('live_birds','dressed') AND deleted_at IS NULL",
            [$batchId]
        );
        return (int) $stmt->fetchColumn();
    }

    public static function soldEggsForBatch(int $batchId): int
    {
        $stmt = DB::getInstance()->query(
            "SELECT COALESCE(SUM(quantity),0) FROM sales WHERE batch_id = ? AND sale_type = 'eggs' AND deleted_at IS NULL",
            [$batchId]
        );
        return (int) $stmt->fetchColumn();
    }

    public static function revenueByType(int $batchId): array
    {
        $stmt = DB::getInstance()->query(
            'SELECT sale_type, SUM(total_amount) as revenue FROM sales WHERE batch_id = ? AND deleted_at IS NULL GROUP BY sale_type',
            [$batchId]
        );
        return $stmt->fetchAll();
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('sales', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('sales', $data, ['id' => $id]);
    }

    public static function softDelete(int $id): int
    {
        return DB::getInstance()->update('sales', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);
    }
}
