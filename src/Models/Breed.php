<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Breed
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('breeds', ['id' => $id]);
    }

    public static function all(): array
    {
        return DB::getInstance()->select('breeds', [], ['order_by' => 'name ASC']);
    }

    public static function create(array $data): int|string
    {
        if (is_array($data['target_weight_by_week'] ?? null)) {
            $data['target_weight_by_week'] = json_encode($data['target_weight_by_week']);
        }
        if (is_array($data['feed_intake_curve'] ?? null)) {
            $data['feed_intake_curve'] = json_encode($data['feed_intake_curve']);
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('breeds', $data);
    }

    public static function update(int $id, array $data): int
    {
        if (is_array($data['target_weight_by_week'] ?? null)) {
            $data['target_weight_by_week'] = json_encode($data['target_weight_by_week']);
        }
        if (is_array($data['feed_intake_curve'] ?? null)) {
            $data['feed_intake_curve'] = json_encode($data['feed_intake_curve']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('breeds', $data, ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('breeds', ['id' => $id]);
    }
}
