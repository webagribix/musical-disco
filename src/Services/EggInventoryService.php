<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Models\EggInventory;

class EggInventoryService
{
    public function reconcile(int $batchId, string $date): void
    {
        $prev    = EggInventory::latestForBatch($batchId);
        $opening = ($prev && $prev['date'] < $date) ? (int) $prev['closing_balance'] : 0;

        $stmt = DB::getInstance()->query(
            'SELECT COALESCE(SUM(good_eggs),0) as collected, COALESCE(SUM(cracked_eggs),0)+COALESCE(SUM(dirty_eggs),0)+COALESCE(SUM(small_eggs),0) as damaged FROM egg_collection_logs WHERE batch_id = ? AND DATE(collected_at) = ?',
            [$batchId, $date]
        );
        $row       = $stmt->fetch();
        $collected = (int) $row['collected'];
        $damaged   = (int) $row['damaged'];

        $soldStmt = DB::getInstance()->query(
            "SELECT COALESCE(SUM(quantity),0) FROM sales WHERE batch_id = ? AND sale_type = 'eggs' AND DATE(sale_date) = ? AND deleted_at IS NULL",
            [$batchId, $date]
        );
        $sold    = (int) $soldStmt->fetchColumn();
        $closing = max(0, $opening + $collected - $sold - $damaged);

        EggInventory::upsert([
            'batch_id'        => $batchId,
            'date'            => $date,
            'opening_balance' => $opening,
            'collected'       => $collected,
            'sold'            => $sold,
            'damaged'         => $damaged,
            'closing_balance' => $closing,
        ]);
    }
}
