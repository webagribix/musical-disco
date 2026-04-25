<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use App\Models\Batch;
use App\Models\MortalityLog;

class OutbreakAlertService
{
    public function __construct(
        private LiveCountService    $liveCountService,
        private NotificationService $notificationService
    ) {}

    public function check(): void
    {
        $batches = DB::getInstance()->selectWhere(
            'batches',
            'deleted_at IS NULL AND status = ?',
            ['active']
        );

        foreach ($batches as $batch) {
            $batchId   = (int) $batch['id'];
            $mortality = MortalityLog::sumLast48h($batchId);
            $liveCount = $this->liveCountService->getLiveCount($batchId);

            if ($liveCount <= 0) continue;

            $rate = $mortality / $liveCount;
            if ($rate >= MORTALITY_ALERT_THRESHOLD) {
                $message = sprintf(
                    'ALERT: Batch "%s" has %.1f%% mortality in 48h (%d birds). Possible disease outbreak.',
                    $batch['batch_name'],
                    $rate * 100,
                    $mortality
                );
                $this->notificationService->createInAppAlert($batchId, 'mortality_outbreak', $message);
            }
        }
    }

    public function checkBatch(int $batchId): bool
    {
        $mortality = MortalityLog::sumLast48h($batchId);
        $liveCount = $this->liveCountService->getLiveCount($batchId);
        if ($liveCount <= 0) return false;
        return ($mortality / $liveCount) >= MORTALITY_ALERT_THRESHOLD;
    }
}
