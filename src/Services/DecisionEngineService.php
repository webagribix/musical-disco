<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Models\Rule;
use App\Models\MortalityLog;
use App\Models\FeedConsumptionLog;
use App\Models\WaterConsumptionLog;
use App\Models\EnvironmentLog;

class DecisionEngineService
{
    public function __construct(
        private LiveCountService    $liveCountService,
        private NotificationService $notificationService
    ) {}

    public function run(): void
    {
        $rules   = Rule::all();
        $batches = DB::getInstance()->selectWhere(
            'batches',
            'deleted_at IS NULL AND status = ?',
            ['active']
        );

        foreach ($batches as $batch) {
            $batchId = (int) $batch['id'];
            foreach ($rules as $rule) {
                $this->evaluateRule($rule, $batch);
            }
        }
    }

    private function evaluateRule(array $rule, array $batch): void
    {
        $batchId    = (int) $batch['id'];
        $metric     = $rule['metric'];
        $condition  = $rule['condition'];
        $threshold  = (float) $rule['threshold_value'];
        $windowHours= (int) ($rule['time_window_hours'] ?? 48);

        $value = match ($metric) {
            'mortality'   => $this->getMortalityRate($batchId, $windowHours),
            'feed'        => $this->getFeedDropPct($batchId),
            'eggs'        => $this->getEggDropPct($batchId),
            'temperature' => $this->getLatestTemperature($batch['house_id'] ?? 0),
            'water'       => $this->getWaterDropPct($batchId),
            default       => null,
        };

        if ($value === null) return;

        $triggered = match ($condition) {
            'gt'       => $value > $threshold,
            'lt'       => $value < $threshold,
            'drop_pct' => $value >= $threshold,
            default    => false,
        };

        if ($triggered) {
            $message = sprintf(
                '[Auto] Rule "%s" triggered for batch "%s": %s = %.2f (threshold: %.2f)',
                $rule['name'] ?? $metric,
                $batch['batch_name'],
                $metric,
                $value,
                $threshold
            );
            $this->notificationService->createInAppAlert($batchId, "rule_{$metric}", $message);
        }
    }

    private function getMortalityRate(int $batchId, int $windowHours): float
    {
        $since     = date('Y-m-d H:i:s', strtotime("-{$windowHours} hours"));
        $stmt      = DB::getInstance()->query(
            'SELECT COALESCE(SUM(`count`),0) FROM mortality_logs WHERE batch_id = ? AND recorded_at >= ?',
            [$batchId, $since]
        );
        $mortality = (int) $stmt->fetchColumn();
        $liveCount = $this->liveCountService->getLiveCount($batchId);
        return $liveCount > 0 ? ($mortality / $liveCount) * 100 : 0.0;
    }

    private function getFeedDropPct(int $batchId): float
    {
        $yesterday = DB::getInstance()->query(
            'SELECT COALESCE(SUM(quantity_kg),0) FROM feed_consumption_logs WHERE batch_id = ? AND DATE(fed_at) = ?',
            [$batchId, date('Y-m-d', strtotime('-1 day'))]
        )->fetchColumn();

        $avg7 = FeedConsumptionLog::avg7DayForBatch($batchId);
        if ($avg7 <= 0) return 0.0;
        return max(0, (($avg7 - (float)$yesterday) / $avg7) * 100);
    }

    private function getEggDropPct(int $batchId): float
    {
        $yesterday = DB::getInstance()->query(
            'SELECT COALESCE(SUM(good_eggs),0) FROM egg_collection_logs WHERE batch_id = ? AND DATE(collected_at) = ?',
            [$batchId, date('Y-m-d', strtotime('-1 day'))]
        )->fetchColumn();

        $since = date('Y-m-d', strtotime('-7 days'));
        $avg7  = DB::getInstance()->query(
            'SELECT AVG(daily) FROM (SELECT DATE(collected_at) d, SUM(good_eggs) daily FROM egg_collection_logs WHERE batch_id = ? AND DATE(collected_at) >= ? GROUP BY DATE(collected_at)) t',
            [$batchId, $since]
        )->fetchColumn();

        if (!$avg7 || $avg7 <= 0) return 0.0;
        return max(0, (($avg7 - (float)$yesterday) / $avg7) * 100);
    }

    private function getLatestTemperature(int $houseId): float
    {
        if (!$houseId) return 0.0;
        $row = DB::getInstance()->selectWhere(
            'environment_logs',
            'house_id = ?',
            [$houseId],
            ['order_by' => 'recorded_at DESC', 'limit' => 1]
        )[0] ?? null;
        return $row ? (float) $row['temperature'] : 0.0;
    }

    private function getWaterDropPct(int $batchId): float
    {
        $yesterday = DB::getInstance()->query(
            'SELECT COALESCE(SUM(quantity_liters),0) FROM water_consumption_logs WHERE batch_id = ? AND DATE(recorded_at) = ?',
            [$batchId, date('Y-m-d', strtotime('-1 day'))]
        )->fetchColumn();

        $avg7 = WaterConsumptionLog::avg7Day($batchId);
        if ($avg7 <= 0) return 0.0;
        return max(0, (($avg7 - (float)$yesterday) / $avg7) * 100);
    }
}
