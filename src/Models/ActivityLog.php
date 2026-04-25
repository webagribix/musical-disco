<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class ActivityLog
{
    public static function log(
        ?int   $userId,
        string $action,
        string $entity,
        ?int   $entityId  = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $ip        = '0.0.0.0'
    ): void {
        DB::getInstance()->insert('activity_logs', [
            'user_id'    => $userId,
            'action'     => $action,
            'entity'     => $entity,
            'entity_id'  => $entityId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function recent(int $limit = 50): array
    {
        return DB::getInstance()->select('activity_logs', [], ['order_by' => 'created_at DESC', 'limit' => $limit]);
    }
}
