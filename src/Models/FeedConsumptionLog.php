<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class FeedConsumptionLog
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('feed_consumption_logs', ['id' => $id]);
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('feed_consumption_logs', ['batch_id' => $batchId], $page, $perPage, [
            'order_by' => 'fed_at DESC',
        ]);
    }

    public static function totalForBatch(int $batchId): float
    {
        return DB::getInstance()->sum('feed_consumption_logs', 'quantity_kg', 'batch_id = ?', [$batchId]);
    }

    public static function avg7DayForBatch(int $batchId): float
    {
        $since = date('Y-m-d', strtotime('-7 days'));
        $stmt  = DB::getInstance()->query(
            'SELECT AVG(daily_total) FROM (SELECT DATE(fed_at) d, SUM(quantity_kg) daily_total FROM feed_consumption_logs WHERE batch_id = ? AND DATE(fed_at) >= ? GROUP BY DATE(fed_at)) t',
            [$batchId, $since]
        );
        return (float) ($stmt->fetchColumn() ?? 0);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('feed_consumption_logs', $data);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('feed_consumption_logs', ['id' => $id]);
    }

    public static function last14Days(): array
    {
        $since = date('Y-m-d', strtotime('-14 days'));
        $stmt  = DB::getInstance()->query(
            'SELECT DATE(fed_at) as date, SUM(quantity_kg) as total_kg FROM feed_consumption_logs WHERE DATE(fed_at) >= ? GROUP BY DATE(fed_at) ORDER BY date ASC',
            [$since]
        );
        return $stmt->fetchAll();
    }
}
