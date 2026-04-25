<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,Breed};

class BreedController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $res->success(Breed::all());
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = Breed::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireOwner($req, $res);
        $data = $req->body();
        if (empty($data['name'])) $res->unprocessable(['name' => 'Required']);
        $id  = Breed::create($data);
        $row = Breed::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'breeds', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireOwner($req, $res);
        $id  = (int)$req->param('id');
        $old = Breed::find($id);
        if (!$old) $res->notFound('Not found');
        Breed::update($id, $req->body());
        $res->success(Breed::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireOwner($req, $res);
        $id  = (int)$req->param('id');
        $row = Breed::find($id);
        if (!$row) $res->notFound('Not found');
        Breed::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
