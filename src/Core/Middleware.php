<?php
declare(strict_types=1);

namespace App\Core;

class Middleware
{
    /** Authenticate via session (web) or Bearer token (API) */
    public static function auth(Request $request): ?array
    {
        // Try API token first
        $token = $request->bearerToken();
        if ($token) {
            $user = Auth::tokenUser($token);
            if ($user) {
                Auth::setCurrentUser($user);
                return $user;
            }
            return null;
        }

        // Fall back to session
        return Auth::user();
    }

    /** Require authenticated user or abort */
    public static function requireAuth(Request $request, Response $response): array
    {
        $user = static::auth($request);
        if (!$user) {
            if ($request->isApi()) {
                $response->unauthorized('Authentication required');
            }
            $response->redirect('/login');
        }
        return $user;
    }

    /** Require specific role(s) */
    public static function requireRole(string|array $roles, Request $request, Response $response): void
    {
        $user    = static::requireAuth($request, $response);
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array($user['role'], $allowed, true)) {
            if ($request->isApi()) {
                $response->forbidden('Insufficient permissions');
            }
            $response->redirect('/dashboard');
        }
    }

    /** Require owner role */
    public static function requireOwner(Request $request, Response $response): void
    {
        static::requireRole('owner', $request, $response);
    }

    /** Require manager or owner */
    public static function requireManager(Request $request, Response $response): void
    {
        static::requireRole(['owner', 'manager'], $request, $response);
    }
}
