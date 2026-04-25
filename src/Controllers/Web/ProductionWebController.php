<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Middleware,Request,Response};
use App\Models\{EggCollectionLog,WeightLog};

class ProductionWebController
{
    public function eggs(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = EggCollectionLog::forBatch($batchId, $req->page(), $req->perPage());
        $res->view('production/eggs', ['logs' => $result, 'batchId' => $batchId]);
    }

    public function weights(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = WeightLog::forBatch($batchId, $req->page(), $req->perPage());
        $res->view('production/weights', ['logs' => $result, 'batchId' => $batchId]);
    }
}
