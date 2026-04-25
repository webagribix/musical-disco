<?php
declare(strict_types=1);
namespace App\Controllers\Web;
use App\Core\{Auth,Middleware,Request,Response,View};
use App\Models\ActivityLog;

class AuthWebController
{
    public function showLogin(Request $req, Response $res): never
    {
        if (Auth::check()) $res->redirect('/dashboard');
        $res->view('auth/login', ['error' => \App\Core\Session::getFlash('error')]);
    }

    public function login(Request $req, Response $res): never
    {
        $email    = trim((string)$req->post('email', ''));
        $password = (string)$req->post('password', '');

        if (Auth::login($email, $password)) {
            $user = Auth::user();
            ActivityLog::log($user['id'], 'login', 'users', $user['id'], null, null, $req->ip());
            $res->redirect('/dashboard');
        }

        \App\Core\Session::flash('error', 'Invalid email or password');
        $res->redirect('/login');
    }

    public function logout(Request $req, Response $res): never
    {
        $user = Auth::user();
        if ($user) ActivityLog::log($user['id'], 'logout', 'users', $user['id'], null, null, $req->ip());
        Auth::logout();
        $res->redirect('/login');
    }

    public function showRegister(Request $req, Response $res): never
    {
        $res->view('auth/login', ['register' => true]);
    }

    public function register(Request $req, Response $res): never
    {
        $data   = $req->body();
        $errors = [];
        if (empty($data['name']))     $errors[] = 'Name required';
        if (empty($data['email']))    $errors[] = 'Email required';
        if (empty($data['password'])) $errors[] = 'Password required';
        if ($errors) {
            \App\Core\Session::flash('error', implode(', ', $errors));
            $res->redirect('/register');
        }
        $existing = \App\Models\User::findByEmail($data['email']);
        if ($existing) {
            \App\Core\Session::flash('error', 'Email already registered');
            $res->redirect('/register');
        }
        $data['role'] = 'worker';
        \App\Models\User::create($data);
        \App\Core\Session::flash('success', 'Account created. Please log in.');
        $res->redirect('/login');
    }
}
