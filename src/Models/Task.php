<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class Task
{
    public static function find(int $id): ?array
    {
        return DB::getInstance()->selectOne('tasks', ['id' => $id]);
    }

    public static function todayForUser(int $userId): array
    {
        $today = date('Y-m-d');
        return DB::getInstance()->selectWhere(
            'tasks',
            '(assigned_to = ? OR assigned_to IS NULL) AND due_date = ? AND status = ?',
            [$userId, $today, 'pending'],
            ['order_by' => 'task_type ASC']
        );
    }

    public static function all(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $extra    = '1=1';
        $bindings = [];
        if (isset($filters['status'])) {
            $extra .= ' AND status = ?';
            $bindings[] = $filters['status'];
        }
        if (isset($filters['due_date'])) {
            $extra .= ' AND due_date = ?';
            $bindings[] = $filters['due_date'];
        }
        return DB::getInstance()->paginate('tasks', [], $page, $perPage, [
            'extra_where'    => $extra,
            'extra_bindings' => $bindings,
            'order_by'       => 'due_date ASC',
        ]);
    }

    public static function create(array $data): int|string
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return DB::getInstance()->insert('tasks', $data);
    }

    public static function updateStatus(int $id, string $status): int
    {
        return DB::getInstance()->update('tasks', [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return DB::getInstance()->delete('tasks', ['id' => $id]);
    }

    public static function pendingCount(int $userId): int
    {
        $today = date('Y-m-d');
        return DB::getInstance()->count(
            'tasks',
            '(assigned_to = ? OR assigned_to IS NULL) AND due_date = ? AND status = ?',
            [$userId, $today, 'pending']
        );
    }
}
