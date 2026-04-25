<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Middleware,Request,Response};
use App\Models\Task;

class TaskWebController
{
    public function today(Request $req, Response $res): never
    {
        $user  = Middleware::requireAuth($req, $res);
        $tasks = Task::todayForUser((int)$user['id']);
        $res->view('tasks/today', ['tasks' => $tasks]);
    }

    public function updateStatus(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $id     = (int)$req->param('id');
        $status = $req->post('status', 'done');
        Task::updateStatus($id, $status);
        $res->redirect('/tasks');
    }
}
