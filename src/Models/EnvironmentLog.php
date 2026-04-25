<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class EnvironmentLog
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('environment_logs', ['id' => $id]);
    }

    public static function latestPerHouse(): array
    {
        $stmt = DB::getInstance()->query(
            'SELECT e.* FROM environment_logs e INNER JOIN (SELECT house_id, MAX(recorded_at) as max_at FROM environment_logs GROUP BY house_id) latest ON e.house_id = latest.house_id AND e.recorded_at = latest.max_at'
        );
        return $stmt->fetchAll();
    }

    public static function forHouse(int $houseId, int $page = 1, int $perPage = 20): array
    {
        return DB::getInstance()->paginate('environment_logs', ['house_id' => $houseId], $page, $perPage, [
            'order_by' => 'recorded_at DESC',
        ]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('environment_logs', $data);
    }
}
