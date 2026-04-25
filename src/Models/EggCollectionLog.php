<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class EggCollectionLog
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('egg_collection_logs', ['id' => $id]);
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('egg_collection_logs', ['batch_id' => $batchId], $page, $perPage, [
            'order_by' => 'collected_at DESC',
        ]);
    }

    public static function totalForBatch(int $batchId): int
    {
        return (int) DB::getInstance()->sum('egg_collection_logs', 'good_eggs', 'batch_id = ?', [$batchId]);
    }

    public static function todayTotal(): int
    {
        $today = date('Y-m-d');
        $stmt  = DB::getInstance()->query(
            'SELECT COALESCE(SUM(good_eggs),0) FROM egg_collection_logs WHERE DATE(collected_at) = ?',
            [$today]
        );
        return (int) $stmt->fetchColumn();
    }

    public static function last30Days(int $batchId): array
    {
        $since = date('Y-m-d', strtotime('-30 days'));
        $stmt  = DB::getInstance()->query(
            'SELECT DATE(collected_at) as date, SUM(good_eggs) as total FROM egg_collection_logs WHERE batch_id = ? AND DATE(collected_at) >= ? GROUP BY DATE(collected_at) ORDER BY date ASC',
            [$batchId, $since]
        );
        return $stmt->fetchAll();
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('egg_collection_logs', $data);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('egg_collection_logs', ['id' => $id]);
    }
}
