<?php
declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\ActivityLog;
use App\Models\Batch;
use App\Services\FCRService;
use App\Services\LiveCountService;
use App\Services\PLService;
use App\Services\VaccinationScheduleService;

class BatchController
{
    private LiveCountService $liveCount;
    private FCRService       $fcr;
    private PLService        $pl;

    public function __construct()
    {
        $this->liveCount = new LiveCountService();
        $this->fcr       = new FCRService($this->liveCount);
        $this->pl        = new PLService($this->liveCount, $this->fcr);
    }

    public function index(Request $req, Response $res): never
    {
        $user    = Middleware::requireAuth($req, $res);
        $filters = ['stage' => $req->get('stage'), 'house_id' => $req->get('house_id')];
        $result  = Batch::all($req->page(), $req->perPage(), array_filter($filters));
        $res->paginated($result);
    }

    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);

        $data   = $req->body();
        $errors = $this->validateBatch($data);
        if ($errors) $res->unprocessable($errors);

        $id    = Batch::create($data);
        $batch = Batch::find((int) $id);

        // Auto-generate vaccination schedule
        (new VaccinationScheduleService())->generateForBatch((int) $id);

        ActivityLog::log($user['id'], 'create', 'batches', (int) $id, null, $batch, $req->ip());
        $res->success($batch, [], 201);
    }

    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $id    = (int) $req->param('id');
        $batch = Batch::find($id);
        if (!$batch) $res->notFound('Batch not found');

        $batch['live_count'] = $this->liveCount->getLiveCount($id);
        $batch['fcr']        = $this->fcr->calculate($id);
        $res->success($batch);
    }

    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);

        $id    = (int) $req->param('id');
        $batch = Batch::find($id);
        if (!$batch) $res->notFound('Batch not found');

        $data = $req->body();
        unset($data['id'], $data['deleted_at']);
        Batch::update($id, $data);

        ActivityLog::log($user['id'], 'update', 'batches', $id, $batch, $data, $req->ip());
        $res->success(Batch::find($id));
    }

    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireOwner($req, $res);

        $id    = (int) $req->param('id');
        $batch = Batch::find($id);
        if (!$batch) $res->notFound('Batch not found');

        Batch::softDelete($id);
        ActivityLog::log($user['id'], 'delete', 'batches', $id, $batch, null, $req->ip());
        $res->success(['message' => 'Batch deleted']);
    }

    public function graduate(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);

        $id      = (int) $req->param('id');
        $success = Batch::graduate($id);
        if (!$success) $res->error('Cannot graduate batch further or batch not found', 422);

        $batch = Batch::find($id);
        ActivityLog::log($user['id'], 'graduate', 'batches', $id, null, ['stage' => $batch['stage']], $req->ip());
        $res->success($batch);
    }

    public function liveCount(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $id    = (int) $req->param('id');
        $batch = Batch::find($id);
        if (!$batch) $res->notFound('Batch not found');

        $res->success(['live_count' => $this->liveCount->getLiveCount($id)]);
    }

    public function pl(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner', 'manager'], true)) {
            $res->forbidden('Financial reports require manager or owner role');
        }
        $id    = (int) $req->param('id');
        $batch = Batch::find($id);
        if (!$batch) $res->notFound('Batch not found');

        $res->success($this->pl->compute($id));
    }

    public function fcr(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $id    = (int) $req->param('id');
        $batch = Batch::find($id);
        if (!$batch) $res->notFound('Batch not found');

        $res->success(['fcr' => $this->fcr->calculate($id)]);
    }

    private function validateBatch(array $data): array
    {
        $errors = [];
        if (empty($data['batch_name'])) $errors['batch_name'] = 'Batch name is required';
        if (empty($data['initial_count']) || (int) $data['initial_count'] <= 0) $errors['initial_count'] = 'Initial count must be > 0';
        if (empty($data['placement_date'])) $errors['placement_date'] = 'Placement date is required';
        if (empty($data['house_id'])) $errors['house_id'] = 'House is required';
        return $errors;
    }
}
