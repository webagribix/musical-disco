<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Vaccination
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('vaccinations', ['id' => $id]);
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('vaccinations', ['batch_id' => $batchId], $page, $perPage, [
            'order_by' => 'administered_at DESC',
        ]);
    }

    public static function pending(int $batchId): array
    {
        return DB::getInstance()->selectWhere(
            'vaccination_schedules',
            'batch_id = ? AND status = ? AND due_date <= ?',
            [$batchId, 'pending', date('Y-m-d')]
        );
    }

    public static function pendingCount(): int
    {
        return DB::getInstance()->count(
            'vaccination_schedules',
            'status = ? AND due_date <= ?',
            ['pending', date('Y-m-d')]
        );
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('vaccinations', $data);
    }

    public static function update(int $id, array $data): int
    {
        return DB::getInstance()->update('vaccinations', $data, ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('vaccinations', ['id' => $id]);
    }
}
