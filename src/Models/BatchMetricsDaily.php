<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class BatchMetricsDaily
{
    public static function forBatch(int $batchId, int $limit = 30): array
    {
        return DB::getInstance()->select('batch_metrics_daily', ['batch_id' => $batchId], [
            'order_by' => 'date DESC',
            'limit'    => $limit,
        ]);
    }

    public static function upsert(array $data): void
    {
        DB::getInstance()->upsert('batch_metrics_daily', $data, ['batch_id', 'date']);
    }
}
