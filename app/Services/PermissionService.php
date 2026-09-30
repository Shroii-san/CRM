<?php

namespace App\Services;

use App\Models\User;
use App\Models\Menu;

class PermissionService
{
    /**
     * Memeriksa apakah user memiliki hak akses ke menu dan aksi tertentu.
     *
     * @param User $user
     * @param int|string $menuIdOrRoute
     * @param string $action ('view', 'create', 'update', 'edit', 'delete', 'assign')
     * @return bool
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
     * Memeriksa apakah user memiliki setidaknya satu akses pada menu tertentu.
     */
    public function hasAnyPermission(User $user, int|string $menuIdOrRoute): bool
    {
        if ($user->role?->name === 'superadmin') {
            return true;
        }

        if (!$user->role) {
            return false;
        }

        $menu = is_numeric($menuIdOrRoute)
            ? $user->role->menus()->where('menus.id', (int) $menuIdOrRoute)->first()
            : $user->role->menus()->where('menus.route', $menuIdOrRoute)->first();

        if (!$menu) {
            return false;
        }

        $pivot = $menu->pivot;

        return (bool) ($pivot->can_view || $pivot->can_create || $pivot->can_update || $pivot->can_delete);
    }
}
