<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class VaccinationSchedule
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('vaccination_schedules', ['id' => $id]);
    }

    public static function forBatch(int $batchId): array
    {
        return DB::getInstance()->select('vaccination_schedules', ['batch_id' => $batchId], [
            'order_by' => 'due_date ASC',
        ]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('vaccination_schedules', $data);
    }

    public static function update(int $id, array $data): int
    {
        return DB::getInstance()->update('vaccination_schedules', $data, ['id' => $id]);
    }
}
