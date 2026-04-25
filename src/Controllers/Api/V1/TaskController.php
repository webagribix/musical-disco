<?php
declare(strict_types=1);
namespace App\Controllers\Api\V1;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,Task};

class TaskController
{
    public function index(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $filters = array_filter(['status' => $req->get('status'), 'due_date' => $req->get('due_date')]);
        $result  = Task::all($req->page(), $req->perPage(), $filters);
        $res->paginated($result);
    }
    public function show(Request $req, Response $res): never
    {
        Middleware::requireAuth($req, $res);
        $row = Task::find((int)$req->param('id'));
        if (!$row) $res->notFound('Not found');
        $res->success($row);
    }
    public function store(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        $data = $req->body();
        $errors = [];
        if (empty($data['task_type'])) $errors['task_type'] = 'Required';
        if (empty($data['due_date']))  $errors['due_date']  = 'Required';
        if ($errors) $res->unprocessable($errors);
        $data['status'] = $data['status'] ?? 'pending';
        $id  = Task::create($data);
        $row = Task::find((int)$id);
        ActivityLog::log($user['id'], 'create', 'tasks', (int)$id, null, $row, $req->ip());
        $res->success($row, [], 201);
    }
    public function updateStatus(Request $req, Response $res): never
    {
        $user   = Middleware::requireAuth($req, $res);
        $id     = (int)$req->param('id');
        $status = $req->input('status', 'done');
        if (!in_array($status, ['pending','done','skipped'], true)) {
            $res->unprocessable(['status' => 'Invalid status']);
        }
        Task::updateStatus($id, $status);
        $res->success(Task::find($id));
    }
    public function destroy(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        Middleware::requireManager($req, $res);
        $id  = (int)$req->param('id');
        $row = Task::find($id);
        if (!$row) $res->notFound('Not found');
        Task::delete($id);
        $res->success(['message' => 'Deleted']);
    }
}
