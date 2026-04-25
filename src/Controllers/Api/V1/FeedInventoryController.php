<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,FeedInventory};

class FeedInventoryController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $result = FeedInventory::all($req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = FeedInventory::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $data = $req->body();
        if (empty($data['feed_type']) || !isset($data['quantity_kg'])) {
            $res->unprocessable(['feed_type' => 'Required', 'quantity_kg' => 'Required']);
        }
        $id  = FeedInventory::create($data);
        $row = FeedInventory::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'feed_inventory', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $old = FeedInventory::find($id);
        if (!$old) $res->notFound('Not found');
        FeedInventory::update($id, $req->body());
        ActivityLog::log($user['id'], 'update', 'feed_inventory', $id, $old, $req->body(), $req->ip());
        $res->success(FeedInventory::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $row = FeedInventory::find($id);
        if (!$row) $res->notFound('Not found');
        FeedInventory::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
