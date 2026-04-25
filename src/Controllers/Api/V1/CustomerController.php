<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,Customer};

class CustomerController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $result = Customer::all($req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = Customer::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $data = $req->body();
        if (empty($data['name'])) $res->unprocessable(['name' => 'Required']);
        $id  = Customer::create($data);
        $row = Customer::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'customers', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $old = Customer::find($id);
        if (!$old) $res->notFound('Not found');
        Customer::update($id, $req->body());
        $res->success(Customer::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $row = Customer::find($id);
        if (!$row) $res->notFound('Not found');
        Customer::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
