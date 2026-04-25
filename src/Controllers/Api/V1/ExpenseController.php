<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,Expense};

class ExpenseController
{
    public function index(Request $req, Response $res): never
    {
        $user    = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();
        $batchId = (int)$req->get('batch_id', 0);
        $result  = Expense::forBatch($batchId, $req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();
        $row = Expense::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $data = $req->body();
        $errors = [];
        if (empty($data['batch_id'])) $errors['batch_id'] = 'Required';
        if (empty($data['amount']))   $errors['amount']   = 'Required';
        if (empty($data['category'])) $errors['category'] = 'Required';
        if ($errors) $res->unprocessable($errors);
        $id  = Expense::create($data);
        $row = Expense::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'expenses', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $old = Expense::find($id);
        if (!$old) $res->notFound('Not found');
        Expense::update($id, $req->body());
        ActivityLog::log($user['id'], 'update', 'expenses', $id, $old, $req->body(), $req->ip());
        $res->success(Expense::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireOwner($req, $res);
        $id  = (int)$req->param('id');
        $row = Expense::find($id);
        if (!$row) $res->notFound('Not found');
        Expense::softDelete($id);
        ActivityLog::log($user['id'], 'delete', 'expenses', $id, $row, null, $req->ip());
        $res->success(['message' => 'Deleted']);
    }
}
