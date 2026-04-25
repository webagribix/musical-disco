<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,FeedConsumptionLog};

class FeedConsumptionController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = FeedConsumptionLog::forBatch($batchId, $req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = FeedConsumptionLog::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $data = $req->body();
        if (empty($data['batch_id']) || empty($data['quantity_kg'])) {
            $res->unprocessable(['batch_id' => 'Required', 'quantity_kg' => 'Required']);
        }
        $id  = FeedConsumptionLog::create($data);
        $row = FeedConsumptionLog::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'feed_consumption_logs', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $row = FeedConsumptionLog::find($id);
        if (!$row) $res->notFound('Not found');
        FeedConsumptionLog::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
