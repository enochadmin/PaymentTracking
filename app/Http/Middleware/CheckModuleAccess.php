<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Admin has access to everything
        if ($user->hasRole('admin')) {
            return $next($request);
        }
        
        // Check if user has permission to access this module
        $permission = $module . '.access';
        
        if (!$user->hasPermission($permission)) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this module.');
        }
        
        return $next($request);
    }
}
