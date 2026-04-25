<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Middleware,Request,Response};
use App\Models\{FeedConsumptionLog,FeedInventory};

class FeedWebController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = FeedConsumptionLog::forBatch($batchId, $req->page(), $req->perPage());
        $res->view('feed/index', ['logs' => $result, 'batchId' => $batchId]);
    }

    public function inventory(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $result = FeedInventory::all($req->page(), $req->perPage());
        $res->view('feed/inventory', ['inventory' => $result]);
    }
}
