<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,Purchase};

class PurchaseController
{
    public function index(Request $req, Response $res): never
    {
        $user   = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();
        $result = Purchase::all($req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();
        $row = Purchase::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $data = $req->body();
        if (empty($data['item_name']) || empty($data['total_amount'])) {
            $res->unprocessable(['item_name' => 'Required', 'total_amount' => 'Required']);
        }
        $id  = Purchase::create($data);
        $row = Purchase::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'purchases', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireOwner($req, $res);
        $id  = (int)$req->param('id');
        $row = Purchase::find($id);
        if (!$row) $res->notFound('Not found');
        Purchase::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
