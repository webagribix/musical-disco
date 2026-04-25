<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\BatchMetricsDaily;
use App\Models\EggCollectionLog;
use App\Models\Expense;

class UnitEconomicsService
{
    public function __construct(
        private LiveCountService $liveCountService,
        private FCRService       $fcrService
    ) {}

    public function computeDaily(int $batchId, string $date): array
    {
        $liveCount     = $this->liveCountService->getLiveCount($batchId);
        $totalExpenses = Expense::totalForBatch($batchId);
        $totalEggs     = EggCollectionLog::totalForBatch($batchId);
        $fcr           = $this->fcrService->calculate($batchId);

        $costPerBird = $liveCount > 0 ? $totalExpenses / $liveCount : 0.0;
        $costPerEgg  = $totalEggs  > 0 ? $totalExpenses / $totalEggs  : 0.0;

        $metrics = [
            'batch_id'      => $batchId,
            'date'          => $date,
            'cost_per_bird' => round($costPerBird, 2),
            'cost_per_egg'  => round($costPerEgg, 2),
            'fcr'           => $fcr,
            'live_count'    => $liveCount,
        ];

        BatchMetricsDaily::upsert($metrics);
        return $metrics;
    }
}
