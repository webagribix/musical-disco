<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\Alert;

class AlertController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $filters = array_filter(['batch_id' => $req->get('batch_id'), 'resolved' => $req->get('resolved')]);
        $result  = Alert::all($req->page(), $req->perPage(), $filters);
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = Alert::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function markRead(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $id = (int)$req->param('id');
        Alert::markRead($id);
        $res->success(['message' => 'Marked as read']);
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $row = Alert::find($id);
        if (!$row) $res->notFound('Not found');
        Alert::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
