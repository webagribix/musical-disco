<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response,DB};
use App\Models\{ActivityLog,House};

class HouseController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $res->success(House::all());
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = House::find((int)$req->param('id'));
        if (!$row) $res->notFound('House not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $data = $req->body();
        if (empty($data['name'])) $res->unprocessable(['name' => 'Name is required']);
        $id  = House::create($data);
        $row = House::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'houses', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id = (int)$req->param('id');
        $old = House::find($id);
        if (!$old) $res->notFound('House not found');
        House::update($id, $req->body());
        ActivityLog::log($user['id'], 'update', 'houses', $id, $old, $req->body(), $req->ip());
        $res->success(House::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireOwner($req, $res);
        $id = (int)$req->param('id');
        $row = House::find($id);
        if (!$row) $res->notFound('House not found');
        House::delete($id);
        ActivityLog::log($user['id'], 'delete', 'houses', $id, $row, null, $req->ip());
        $res->success(['message' => 'Deleted']);
    }
}
