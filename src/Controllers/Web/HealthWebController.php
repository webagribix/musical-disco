<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Middleware,Request,Response,DB};
use App\Models\{Alert,Vaccination,MedicationLog};

class HealthWebController
{
    public function vaccinations(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = Vaccination::forBatch($batchId);
        $res->view('health/vaccinations', ['vaccinations' => $result, 'batchId' => $batchId]);
    }

    public function medications(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = MedicationLog::forBatch($batchId);
        $res->view('health/medications', ['medications' => $result['data'] ?? $result, 'batchId' => $batchId]);
    }

    public function alerts(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $result = Alert::all($req->page(), $req->perPage());
        $res->view('health/alerts', ['alerts' => $result]);
    }
}
