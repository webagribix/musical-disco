<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,EnvironmentLog};

class EnvironmentController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $houseId = (int)$req->get('house_id', 0);
        $result  = EnvironmentLog::forHouse($houseId, $req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = EnvironmentLog::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $data = $req->body();
        if (empty($data['house_id'])) $res->unprocessable(['house_id' => 'Required']);
        $id  = EnvironmentLog::create($data);
        $row = EnvironmentLog::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'environment_logs', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
}
