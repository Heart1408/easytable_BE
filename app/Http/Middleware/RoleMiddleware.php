<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        foreach ($roles as $role) {
            if ($user->isAdmin() && $role === 'admin') {
                return $next($request);
            }
            if ($role === 'staff') {
                return $next($request);
            }
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }
}