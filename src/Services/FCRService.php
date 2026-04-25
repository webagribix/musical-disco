<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Batch;
use App\Models\FeedConsumptionLog;
use App\Models\WeightLog;

class FCRService
{
    public function __construct(private LiveCountService $liveCountService) {}

    public function calculate(int $batchId): float
    {
        $batch = Batch::find($batchId);
        if (!$batch) return 0.0;

        $totalFeed    = FeedConsumptionLog::totalForBatch($batchId);
        $latestWeight = WeightLog::latestForBatch($batchId);
        $liveCount    = $this->liveCountService->getLiveCount($batchId);
        $initialCount = (int) $batch['initial_count'];

        $initialWeight = 0.04; // 40g chick weight in kg
        $currentWeight = $latestWeight ? (float) $latestWeight['average_weight_kg'] : 0.0;

        $denominator = ($currentWeight * $liveCount) - ($initialWeight * $initialCount);
        if ($denominator <= 0) return 0.0;

        return round($totalFeed / $denominator, 3);
    }
}
