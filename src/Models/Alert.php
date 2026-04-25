<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Alert
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('alerts', ['id' => $id]);
    }

    public static function all(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $extra    = '1=1';
        $bindings = [];
        if (isset($filters['batch_id'])) {
            $extra .= ' AND batch_id = ?';
            $bindings[] = (int) $filters['batch_id'];
        }
        if (isset($filters['resolved'])) {
            $extra .= ' AND is_resolved = ?';
            $bindings[] = (int) $filters['resolved'];
        }
        return DB::getInstance()->paginate('alerts', [], $page, $perPage, [
            'extra_where'    => $extra,
            'extra_bindings' => $bindings,
            'order_by'       => 'created_at DESC',
        ]);
    }

    public static function activeCount(): int
    {
        return DB::getInstance()->count('alerts', 'is_resolved = 0');
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('alerts', $data);
    }

    public static function markRead(int $id): int
    {
        return DB::getInstance()->update('alerts', ['is_resolved' => 1, 'resolved_at' => date('Y-m-d H:i:s')], ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('alerts', ['id' => $id]);
    }
}
