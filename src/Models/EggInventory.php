<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

class EggInventory
{
    public static function latestForBatch(int $batchId): ?array
    {
        $rows = DB::getInstance()->select('egg_inventory', ['batch_id' => $batchId], [
            'order_by' => 'date DESC',
            'limit'    => 1,
        ]);
        return $rows[0] ?? null;
    }

    public static function forDate(int $batchId, string $date): ?array
    {
        $rows = DB::getInstance()->selectWhere(
            'egg_inventory',
            'batch_id = ? AND date = ?',
            [$batchId, $date]
        );
        return $rows[0] ?? null;
    }

    public static function upsert(array $data): void
    {
        DB::getInstance()->upsert('egg_inventory', $data, ['batch_id', 'date']);
    }
}
