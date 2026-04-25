<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,Vaccination};

class VaccinationController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batchId = (int)$req->get('batch_id', 0);
        $result  = Vaccination::forBatch($batchId, $req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = Vaccination::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $data = $req->body();
        if (empty($data['batch_id']) || empty($data['vaccine_name'])) {
            $res->unprocessable(['batch_id' => 'Required', 'vaccine_name' => 'Required']);
        }
        $id  = Vaccination::create($data);
        $row = Vaccination::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'vaccinations', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $old = Vaccination::find($id);
        if (!$old) $res->notFound('Not found');
        Vaccination::update($id, $req->body());
        ActivityLog::log($user['id'], 'update', 'vaccinations', $id, $old, $req->body(), $req->ip());
        $res->success(Vaccination::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $row = Vaccination::find($id);
        if (!$row) $res->notFound('Not found');
        Vaccination::delete($id);
        ActivityLog::log($user['id'], 'delete', 'vaccinations', $id, $row, null, $req->ip());
        $res->success(['message' => 'Deleted']);
    }
}
