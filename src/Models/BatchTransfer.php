<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class BatchTransfer
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('batch_transfers', ['id' => $id]);
    }

    public static function forBatch(int $batchId): array
    {
        return DB::getInstance()->select('batch_transfers', ['batch_id' => $batchId], [
            'order_by' => 'transferred_at DESC',
        ]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('batch_transfers', $data);
    }
}
