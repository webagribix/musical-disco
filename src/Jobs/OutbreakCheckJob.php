<?php
declare(strict_types=1);
namespace App\Jobs;
use App\Services\{LiveCountService,NotificationService,OutbreakAlertService};

class OutbreakCheckJob
{
    public function handle(): void
    {
        $lc  = new LiveCountService();
        $ns  = new NotificationService();
        $svc = new OutbreakAlertService($lc, $ns);
        $svc->check();
    }
}
