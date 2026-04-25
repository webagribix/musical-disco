<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class VetVisit
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('vet_visits', ['id' => $id]);
    }

    public static function forBatch(int $batchId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('vet_visits', ['batch_id' => $batchId], $page, $perPage, [
            'order_by' => 'visit_date DESC',
        ]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('vet_visits', $data);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('vet_visits', ['id' => $id]);
    }
}
