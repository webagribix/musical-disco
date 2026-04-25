<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,WaterConsumptionLog};

class WaterConsumptionController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = WaterConsumptionLog::forBatch($batchId, $req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = WaterConsumptionLog::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $data = $req->body();
        if (empty($data['batch_id']) || empty($data['quantity_liters'])) {
            $res->unprocessable(['batch_id' => 'Required', 'quantity_liters' => 'Required']);
        }
        $id  = WaterConsumptionLog::create($data);
        $row = WaterConsumptionLog::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'water_consumption_logs', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
}
