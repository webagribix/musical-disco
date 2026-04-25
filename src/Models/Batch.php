<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Batch
{
    public const STAGES = ['brooding', 'growing', 'point_of_lay', 'market_ready'];

    public static function find(int $id): ?array
    {
        $db = DB::getInstance();
        return $db->selectWhere('batches', 'id = ? AND deleted_at IS NULL', [$id])[0] ?? null;
    }

    public static function all(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $extra = 'deleted_at IS NULL';
        $bindings = [];
        if (!empty($filters['stage'])) {
            $extra .= ' AND stage = ?';
            $bindings[] = $filters['stage'];
        }
        if (!empty($filters['house_id'])) {
            $extra .= ' AND house_id = ?';
            $bindings[] = (int) $filters['house_id'];
        }
        return DB::getInstance()->paginate('batches', [], $page, $perPage, [
            'extra_where'    => $extra,
            'extra_bindings' => $bindings,
            'order_by'       => 'created_at DESC',
        ]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('batches', $data);
    }

    public static function update(int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->update('batches', $data, ['id' => $id]);
    }

    public static function softDelete(int $id): int
    {
        return DB::getInstance()->update('batches', [
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
    }

    public static function graduate(int $id): bool
    {
        $batch = static::find($id);
        if (!$batch) return false;

        $idx     = array_search($batch['stage'], static::STAGES, true);
        $nextIdx = $idx + 1;
        if ($nextIdx >= count(static::STAGES)) return false;

        static::update($id, ['stage' => static::STAGES[$nextIdx]]);
        return true;
    }

    public static function activeCount(): int
    {
        return DB::getInstance()->count('batches', 'deleted_at IS NULL AND status = ?', ['active']);
    }
}
