<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Middleware,Request,Response};
use App\Models\{Alert,EggCollectionLog,MortalityLog,Task,Vaccination};
use App\Services\LiveCountService;

class DashboardController
{
    public function index(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $liveCountSvc = new LiveCountService();
        $db = \App\Core\DB::getInstance();

        $batches   = $db->selectWhere('batches', 'deleted_at IS NULL AND status = ?', ['active']);
        $totalLive = 0;
        foreach ($batches as $b) $totalLive += $liveCountSvc->getLiveCount((int)$b['id']);

        $envLogs    = \App\Models\EnvironmentLog::latestPerHouse();
        $todayTasks = Task::todayForUser((int)$user['id']);

        $eggChart = $db->query(
            'SELECT DATE(collected_at) as date, SUM(good_eggs) as total FROM egg_collection_logs WHERE DATE(collected_at) >= ? GROUP BY DATE(collected_at) ORDER BY date ASC',
            [date('Y-m-d', strtotime('-30 days'))]
        )->fetchAll();

        $feedChart = \App\Models\FeedConsumptionLog::last14Days();

        $res->view('dashboard/index', [
            'totalLive'          => $totalLive,
            'todayMortality'     => MortalityLog::todayCount(),
            'todayEggs'          => EggCollectionLog::todayTotal(),
            'pendingVaccinations'=> Vaccination::pendingCount(),
            'activeAlerts'       => Alert::activeCount(),
            'activeBatches'      => count($batches),
            'envLogs'            => $envLogs,
            'todayTasks'         => $todayTasks,
            'eggChart'           => json_encode($eggChart),
            'feedChart'          => json_encode($feedChart),
        ]);
    }
}
