<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class WaterConsumptionLog
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('water_consumption_logs', ['id' => $id]);
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('water_consumption_logs', ['batch_id' => $batchId], $page, $perPage, [
            'order_by' => 'recorded_at DESC',
        ]);
    }

    public static function avg7Day(int $batchId): float
    {
        $since = date('Y-m-d', strtotime('-7 days'));
        $stmt  = DB::getInstance()->query(
            'SELECT AVG(quantity_liters) FROM water_consumption_logs WHERE batch_id = ? AND DATE(recorded_at) >= ?',
            [$batchId, $since]
        );
        return (float) ($stmt->fetchColumn() ?? 0);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('water_consumption_logs', $data);
    }
}
