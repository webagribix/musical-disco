<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class MortalityLog
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('mortality_logs', ['id' => $id]);
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('mortality_logs', ['batch_id' => $batchId], $page, $perPage, [
            'order_by' => 'recorded_at DESC',
        ]);
    }

    public static function sumForBatch(int $batchId): int
    {
        return (int) DB::getInstance()->sum('mortality_logs', 'count', 'batch_id = ?', [$batchId]);
    }

    public static function sumLast48h(int $batchId): int
    {
        $since = date('Y-m-d H:i:s', strtotime('-48 hours'));
        $stmt  = DB::getInstance()->query(
            'SELECT COALESCE(SUM(`count`),0) FROM mortality_logs WHERE batch_id = ? AND recorded_at >= ?',
            [$batchId, $since]
        );
        return (int) $stmt->fetchColumn();
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('mortality_logs', $data);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('mortality_logs', ['id' => $id]);
    }

    public static function todayCount(): int
    {
        $today = date('Y-m-d');
        $stmt  = DB::getInstance()->query(
            'SELECT COALESCE(SUM(`count`),0) FROM mortality_logs WHERE DATE(recorded_at) = ?',
            [$today]
        );
        return (int) $stmt->fetchColumn();
    }
}
