<?php
declare(strict_types=1);
namespace App\Jobs;
use App\Services\{DecisionEngineService,LiveCountService,NotificationService};

class DecisionEngineJob
{
    public function handle(): void
    {
        $lc  = new LiveCountService();
        $ns  = new NotificationService();
        $svc = new DecisionEngineService($lc, $ns);
        $svc->run();
    }
}
