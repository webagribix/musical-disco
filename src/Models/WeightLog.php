<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class WeightLog
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('weight_logs', ['id' => $id]);
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('weight_logs', ['batch_id' => $batchId], $page, $perPage, [
            'order_by' => 'weighed_at DESC',
        ]);
    }

    public static function latestForBatch(int $batchId): ?array
    {
        $rows = DB::getInstance()->select('weight_logs', ['batch_id' => $batchId], [
            'order_by' => 'weighed_at DESC',
            'limit'    => 1,
        ]);
        return $rows[0] ?? null;
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('weight_logs', $data);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('weight_logs', ['id' => $id]);
    }
}
