<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Middleware,Request,Response};
use App\Models\{Batch,Breed,House};
use App\Services\{FCRService,LiveCountService,QRCodeService,VaccinationScheduleService};

class BatchWebController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $result = Batch::all($req->page(), $req->perPage());
        $res->view('batches/index', ['batches' => $result]);
    }

    public function create(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $res->view('batches/create', ['breeds' => Breed::all(), 'houses' => House::all()]);
    }

    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        \App\Core\Middleware::requireManager($req, $res);
        $data = $req->body();
        $id   = Batch::create($data);
        (new VaccinationScheduleService())->generateForBatch((int)$id);
        \App\Models\ActivityLog::log($user['id'], 'create', 'batches', (int)$id, null, $data, $req->ip());
        $res->redirect('/batches/' . $id);
    }

    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $id    = (int)$req->param('id');
        $batch = Batch::find($id);
        if (!$batch) { http_response_code(404); $res->view('layout/404'); }

        $liveCountSvc = new LiveCountService();
        $fcrSvc       = new FCRService($liveCountSvc);
        $qrSvc        = new QRCodeService();

        $house  = $batch['house_id'] ? House::find((int)$batch['house_id']) : null;
        $qrCode = $house ? $qrSvc->generateForHouse((int)$batch['house_id'], $house['name'] ?? '') : null;

        $res->view('batches/show', [
            'batch'     => $batch,
            'liveCount' => $liveCountSvc->getLiveCount($id),
            'fcr'       => $fcrSvc->calculate($id),
            'transfers' => \App\Models\BatchTransfer::forBatch($id),
            'vaccSchedule' => (new VaccinationScheduleService())->getPendingForBatch($id),
            'qrCode'    => $qrCode,
        ]);
    }

    public function edit(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $batch = Batch::find((int)$req->param('id'));
        if (!$batch) $res->redirect('/batches');
        $res->view('batches/create', ['batch' => $batch, 'breeds' => Breed::all(), 'houses' => House::all(), 'edit' => true]);
    }

    public function update(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $id   = (int)$req->param('id');
        $data = $req->body();
        unset($data['id'], $data['deleted_at']);
        Batch::update($id, $data);
        \App\Models\ActivityLog::log($user['id'], 'update', 'batches', $id, null, $data, $req->ip());
        $res->redirect('/batches/' . $id);
    }
}
