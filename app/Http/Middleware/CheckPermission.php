<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     */
    public function handle(Request $request, Closure $next, ?string $permission = null)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403);
        }

        // admins bypass permissions
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return $next($request);
        }

        if ($permission && method_exists($user, 'hasPermission') && $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403);
    }
}
