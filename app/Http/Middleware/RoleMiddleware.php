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

        if (!in_array($user->role, $roles)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}