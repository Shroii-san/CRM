<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Role;
use App\Models\Menu;
use App\Services\PermissionService;
use Carbon\Carbon;

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
        // Superadmin selalu bisa akses apa saja
        Gate::before(function ($user, $ability) {
            if ($user->role?->name === 'superadmin') {
                return true;
            }
        });

        // Gate kustom untuk cek menu
        Gate::define('access-menu', function ($user, $menuId, $action) {
            return app(PermissionService::class)->hasPermission($user, $menuId, $action);
        });
    }
}

