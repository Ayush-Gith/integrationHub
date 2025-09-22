<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }
        
        // Support either `role` column or `is_admin` boolean
        $hasRole = false;

        if ($role === 'admin') {
            $hasRole = ($user->role === 'admin') || ($user->is_admin == 1);
        } else {
            $hasRole = $user->role === $role;
        }

        if (!$hasRole) {
            abort(403, 'This action is unauthorized.');
        }
        return $next($request);
    }
}