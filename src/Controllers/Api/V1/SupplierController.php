<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,Supplier};

class SupplierController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $result = Supplier::all($req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = Supplier::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $data = $req->body();
        if (empty($data['name'])) $res->unprocessable(['name' => 'Required']);
        $id  = Supplier::create($data);
        $row = Supplier::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'suppliers', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $old = Supplier::find($id);
        if (!$old) $res->notFound('Not found');
        Supplier::update($id, $req->body());
        $res->success(Supplier::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $row = Supplier::find($id);
        if (!$row) $res->notFound('Not found');
        Supplier::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
