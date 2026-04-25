<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response,DB};
use App\Models\{Alert,EggCollectionLog,MortalityLog,Vaccination};
use App\Services\{FCRService,LiveCountService,PLService,PdfService};

class ReportController
{
    public function dashboard(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();

        $db = DB::getInstance();

        // Total live birds across all active batches
        $batches    = $db->selectWhere('batches', 'deleted_at IS NULL AND status = ?', ['active']);
        $liveCountSvc = new LiveCountService();
        $totalLive  = 0;
        foreach ($batches as $b) $totalLive += $liveCountSvc->getLiveCount((int)$b['id']);

        $kpis = [
            'total_live_birds'        => $totalLive,
            'today_mortality'         => MortalityLog::todayCount(),
            'today_eggs_collected'    => EggCollectionLog::todayTotal(),
            'pending_vaccinations'    => Vaccination::pendingCount(),
            'active_alerts'           => Alert::activeCount(),
            'active_batches'          => count($batches),
        ];

        $eggLast30 = DB::getInstance()->query(
            'SELECT DATE(collected_at) as date, SUM(good_eggs) as total FROM egg_collection_logs WHERE DATE(collected_at) >= ? GROUP BY DATE(collected_at) ORDER BY date ASC',
            [date('Y-m-d', strtotime('-30 days'))]
        )->fetchAll();

        $feedLast14 = \App\Models\FeedConsumptionLog::last14Days();

        $res->success([
            'kpis'        => $kpis,
            'egg_chart'   => $eggLast30,
            'feed_chart'  => $feedLast14,
        ]);
    }

    public function batchPl(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();
        $id       = (int)$req->param('id');
        $liveCount= new LiveCountService();
        $fcr      = new FCRService($liveCount);
        $pl       = new PLService($liveCount, $fcr);
        $data     = $pl->compute($id);
        if (!$data) $res->notFound('Batch not found');
        $res->success($data);
    }

    public function batchPdf(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();
        $id       = (int)$req->param('id');
        $liveCount= new LiveCountService();
        $fcr      = new FCRService($liveCount);
        $pl       = new PLService($liveCount, $fcr);
        $data     = $pl->compute($id);
        if (!$data) $res->notFound('Batch not found');
        $pdf = (new PdfService())->generatePLReport($data);
        $res->pdf($pdf, "pl_batch_{$id}.pdf");
    }
}
