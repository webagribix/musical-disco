<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class MedicationLog
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('medication_logs', ['id' => $id]);
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('medication_logs', ['batch_id' => $batchId], $page, $perPage, [
            'order_by' => 'administered_at DESC',
        ]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('medication_logs', $data);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('medication_logs', ['id' => $id]);
    }
}
