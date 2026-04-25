<?php
declare(strict_types=1);

namespace App\Controllers\Api\V1;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Models\ActivityLog;
use App\Models\User;

class AuthController
{
    public function login(Request $req, Response $res): never
    {
        $email    = trim((string) $req->input('email', ''));
        $password = (string) $req->input('password', '');

        $errors = [];
        if (!$email)    $errors['email']    = 'Email is required';
        if (!$password) $errors['password'] = 'Password is required';
        if ($errors) $res->unprocessable($errors);

        if (!Auth::login($email, $password)) {
            $res->error('Invalid credentials', 401);
        }

        $user = Auth::user();
        ActivityLog::log($user['id'], 'login', 'users', $user['id'], null, null, $req->ip());
        $res->success(User::sanitize($user));
    }

    public function logout(Request $req, Response $res): never
    {
        $token = $req->bearerToken();
        if ($token) {
            $user = Auth::tokenUser($token);
            if ($user) {
                ActivityLog::log($user['id'], 'logout', 'users', $user['id'], null, null, $req->ip());
                \App\Core\DB::getInstance()->update('users', ['api_token' => null], ['id' => $user['id']]);
            }
        }
        Auth::logout();
        $res->success(['message' => 'Logged out']);
    }

    public function me(Request $req, Response $res): never
    {
        $token = $req->bearerToken();
        $user  = $token ? Auth::tokenUser($token) : Auth::user();
        if (!$user) $res->unauthorized();
        $res->success(User::sanitize($user));
    }
}
