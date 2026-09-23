<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Services\PermissionService;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil current route name
        $routeName = Route::currentRouteName();
        
        // Skip permission check untuk route cascade dropdown
        $cascadeRoutes = [
            '/get-regencies/',
            '/get-districts/', 
            '/get-villages/'
        ];
        
        $currentPath = $request->getPathInfo();
        foreach ($cascadeRoutes as $cascadeRoute) {
            if (str_contains($currentPath, $cascadeRoute)) {
                return $next($request); // Skip permission check
            }
        }

        // Cari menu berdasarkan route
        $menu = Menu::where('route', $routeName)->first();

        if ($menu) {
            $menuId = $menu->id ?? $menu->menu_id;
            view()->share('currentMenuId', $menuId);

            // Tentukan action berdasarkan HTTP method
            $action = match ($request->method()) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'view',
            };

            // Cek akses user via PermissionService
            if (!$this->permissionService->hasPermission($user, $menuId, $action)) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman atau fitur ini.');
            }
        }

        return $next($request);
    }
}