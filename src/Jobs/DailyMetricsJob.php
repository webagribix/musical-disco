<?php
declare(strict_types=1);
namespace App\Jobs;
use App\Core\DB;
use App\Services\{FCRService,LiveCountService,UnitEconomicsService};

class DailyMetricsJob
{
    public function handle(): void
    {
        $lc  = new LiveCountService();
        $fcr = new FCRService($lc);
        $svc = new UnitEconomicsService($lc, $fcr);

        $batches = DB::getInstance()->selectWhere('batches', 'deleted_at IS NULL AND status = ?', ['active']);
        $date    = date('Y-m-d');
        foreach ($batches as $batch) {
            $svc->computeDaily((int)$batch['id'], $date);
        }
    }
}
