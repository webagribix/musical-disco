<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,EggCollectionLog};
use App\Services\EggInventoryService;

class EggCollectionController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = EggCollectionLog::forBatch($batchId, $req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = EggCollectionLog::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $data = $req->body();
        if (empty($data['batch_id']) || !isset($data['good_eggs'])) {
            $res->unprocessable(['batch_id' => 'Required', 'good_eggs' => 'Required']);
        }
        $id   = EggCollectionLog::create($data);
        $row  = EggCollectionLog::find((int)$id);
        $date = date('Y-m-d');
        (new EggInventoryService())->reconcile((int)$data['batch_id'], $date);
        ActivityLog::log($user['id'], 'create', 'egg_collection_logs', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $row = EggCollectionLog::find($id);
        if (!$row) $res->notFound('Not found');
        EggCollectionLog::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
