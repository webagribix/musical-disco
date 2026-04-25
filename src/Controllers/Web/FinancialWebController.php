<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Middleware,Request,Response};
use App\Models\{Expense,Sale};
use App\Services\{FCRService,LiveCountService,PLService};

class FinancialWebController
{
    public function expenses(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->redirect('/dashboard');
        $batchId = (int)$req->get('batch_id', 0);
        $result  = Expense::forBatch($batchId, $req->page(), $req->perPage());
        $res->view('financial/expenses', ['expenses' => $result, 'batchId' => $batchId]);
    }

    public function sales(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->redirect('/dashboard');
        $batchId = (int)$req->get('batch_id', 0);
        $result  = Sale::forBatch($batchId, $req->page(), $req->perPage());
        $res->view('financial/sales', ['sales' => $result, 'batchId' => $batchId]);
    }

    public function plReport(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        if (!in_array($user['role'], ['owner','manager'], true)) $res->redirect('/dashboard');
        $batchId  = (int)$req->get('batch_id', 0);
        $lc       = new LiveCountService();
        $fcr      = new FCRService($lc);
        $pl       = new PLService($lc, $fcr);
        $plData   = $batchId ? $pl->compute($batchId) : null;
        $batches  = \App\Core\DB::getInstance()->selectWhere('batches', 'deleted_at IS NULL', [], ['order_by' => 'batch_name ASC']);
        $res->view('financial/pl_report', ['plData' => $plData, 'batches' => $batches, 'batchId' => $batchId]);
    }
}
