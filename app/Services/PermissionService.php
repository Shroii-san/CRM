<?php

namespace App\Services;

use App\Models\User;
use App\Models\Menu;

class PermissionService
{
    /**
     * Cek apakah user punya permission ke menu (untuk detail permission check)
     */
    public function hasPermission(User $user, int|string $menuIdOrRoute, string $action): bool
    {
        // 1. Superadmin Bypass
        if ($user->role?->name === 'superadmin') {
            return true;
        }

        if (!$user->role) {
            return false;
        }

        // 2. Normalisasi nama aksi ('edit' -> 'update')
        $normalizedAction = match ($action) {
            'edit' => 'update',
            default => $action,
        };

        // 3. Cari menu berdasarkan ID atau Route/Slug
        $menu = null;
        if (is_numeric($menuIdOrRoute)) {
            $menu = $user->role->relationLoaded('menus')
                ? $user->role->menus->firstWhere('id', (int) $menuIdOrRoute)
                : $user->role->menus()->where('menus.id', (int) $menuIdOrRoute)->first();
        } else {
            $menu = $user->role->relationLoaded('menus')
                ? $user->role->menus->firstWhere('route', $menuIdOrRoute)
                : $user->role->menus()->where('menus.route', $menuIdOrRoute)->first();
        }

        if (!$menu) {
            return false;
        }

        // 4. Ambil nilai kolom pivot (can_view, can_create, can_update, can_delete, can_assign)
        $pivotColumn = 'can_' . $normalizedAction;

        return (bool) ($menu->pivot->{$pivotColumn} ?? false);
    }

    /**
     * Ambil menus untuk sidebar berdasarkan RBAC
     */
    public function getSidebarMenus(User $user)
    {
        if (!$user->role) {
            return collect();
        }

        // Superadmin lihat semua menu
        if ($user->role->name === 'superadmin') {
            return Menu::parentOnly()
                ->where('is_active', true)
                ->with(['icon', 'children'])
                ->orderBy('position')
                ->get();
        }

        // Non-superadmin: filter berdasarkan role_permissions
        $user->role->load('permissions');

        $viewableMenuIds = $user->role->permissions
            ->where('pivot.can_view', true)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        return (bool) ($pivot->can_view || $pivot->can_create || $pivot->can_update || $pivot->can_delete);
    }
}
