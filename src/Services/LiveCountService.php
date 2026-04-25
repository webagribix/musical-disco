<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Models\Batch;
use App\Models\MortalityLog;
use App\Models\Sale;

class LiveCountService
{
    public function getLiveCount(int $batchId): int
    {
        $batch = Batch::find($batchId);
        if (!$batch) return 0;

        $initialCount = (int) $batch['initial_count'];
        $mortality    = MortalityLog::sumForBatch($batchId);
        $soldBirds    = Sale::soldBirdsForBatch($batchId);

        return max(0, $initialCount - $mortality - $soldBirds);
    }

    public function validateTransfer(int $batchId, int $count): bool
    {
        return $count > 0 && $count <= $this->getLiveCount($batchId);
    }

    public function validateSale(int $batchId, int $quantity, string $saleType): bool
    {
        if ($quantity <= 0) return false;
        if (in_array($saleType, ['live_birds', 'dressed'], true)) {
            return $quantity <= $this->getLiveCount($batchId);
        }
        return true;
    }
}
