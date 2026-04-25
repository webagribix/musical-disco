<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,Sale};
use App\Services\LiveCountService;

class SaleController
{
    public function index(Request $req, Response $res): never
    {
        $user    = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();
        $batchId = (int)$req->get('batch_id', 0);
        $result  = Sale::forBatch($batchId, $req->page(), $req->perPage());
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->forbidden();
        $row = Sale::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $data   = $req->body();
        $errors = [];
        if (empty($data['batch_id']))   $errors['batch_id']   = 'Required';
        if (empty($data['sale_type']))  $errors['sale_type']  = 'Required';
        if (empty($data['quantity']))   $errors['quantity']   = 'Required';
        if (empty($data['unit_price'])) $errors['unit_price'] = 'Required';
        if ($errors) $res->unprocessable($errors);
        // validate live count
        $svc = new LiveCountService();
        if (!$svc->validateSale((int)$data['batch_id'], (int)$data['quantity'], $data['sale_type'])) {
            $res->error('Insufficient live birds for this sale', 422);
        }
        $data['total_amount'] = (float)$data['quantity'] * (float)$data['unit_price'];
        $id  = Sale::create($data);
        $row = Sale::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'sales', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $old = Sale::find($id);
        if (!$old) $res->notFound('Not found');
        Sale::update($id, $req->body());
        $res->success(Sale::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireOwner($req, $res);
        $id  = (int)$req->param('id');
        $row = Sale::find($id);
        if (!$row) $res->notFound('Not found');
        Sale::softDelete($id);
        $res->success(['message' => 'Deleted']);
    }
}
