<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,MortalityLog};

class MortalityController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = MortalityLog::forBatch($batchId, $req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = MortalityLog::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $data = $req->body();
        if (empty($data['batch_id']) || empty($data['count'])) {
            $res->unprocessable(['batch_id' => 'Required', 'count' => 'Required']);
        }
        $id  = MortalityLog::create($data);
        $row = MortalityLog::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'mortality_logs', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id = (int)$req->param('id');
        $row = MortalityLog::find($id);
        if (!$row) $res->notFound('Not found');
        MortalityLog::delete($id);
        ActivityLog::log($user['id'], 'delete', 'mortality_logs', $id, $row, null, $req->ip());
        $res->success(['message' => 'Deleted']);
    }
}
