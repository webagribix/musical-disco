<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Middleware,Request,Response};
use App\Models\{ActivityLog,User};

class UserWebController
{
    public function index(Request $req, Response $res): never
    {
        $user = Middleware::requireAuth($req, $res);
        \App\Core\Middleware::requireOwner($req, $res);
        $result = User::all($req->page(), $req->perPage());
        $res->view('users/index', ['users' => $result]);
    }

    public function store(Request $req, Response $res): never
    {
        $currentUser = Middleware::requireAuth($req, $res);
        \App\Core\Middleware::requireOwner($req, $res);
        $data = $req->body();
        $id   = User::create($data);
        ActivityLog::log($currentUser['id'], 'create', 'users', (int)$id, null, ['name' => $data['name']], $req->ip());
        $res->redirect('/users');
    }

    public function update(Request $req, Response $res): never
    {
        $currentUser = Middleware::requireAuth($req, $res);
        \App\Core\Middleware::requireOwner($req, $res);
        $id   = (int)$req->param('id');
        $data = $req->body();
        unset($data['id']);
        User::update($id, $data);
        ActivityLog::log($currentUser['id'], 'update', 'users', $id, null, $data, $req->ip());
        $res->redirect('/users');
    }
}
