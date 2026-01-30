<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register blade aliases for layout components
        Blade::component('layouts.guest', 'guest-layout');
        Blade::component('layouts.app', 'app-layout');

        // Super Admin Access
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        // Dynamic Permission Check
        // If the user has a specific permission via their role, allow it.
        // This makes $user->can('permission.name') work.
        \Illuminate\Support\Facades\Gate::define('default_permission_check', function ($user) {
            return true; 
        }); // Placeholder, effectively we rely on the logic below being called for every 'can' check? No.
        
        // Actually, the best way without defining every single permission string as a key in a loop 
        // (which queries DB on every request) is to use `Gate::after` or `Gate::before` carefully.
        
        // If we use Gate::before, we can return boolean to authorize.
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
            
            // Check if Model has this permission
            // methods available: hasPermission($name)
            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }
        });
    }
}
