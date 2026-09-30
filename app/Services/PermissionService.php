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
     * @param int|string $menuIdOrRoute (menu ID, slug, atau route)
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

        // 3. Load permissions kalau belum
        if (!$user->role->relationLoaded('permissions')) {
            $user->role->load('permissions');
        }

        // 4. Cari menu berdasarkan ID, Slug, atau Route
        $menu = null;
        if (is_numeric($menuIdOrRoute)) {
            $menu = $user->role->permissions->firstWhere('id', (int) $menuIdOrRoute);
        } else {
            $menu = $user->role->permissions->firstWhere('slug', $menuIdOrRoute)
                ?? $user->role->permissions->firstWhere('route', $menuIdOrRoute);
        }

        if (!$menu) {
            return false;
        }

        // 5. Ambil nilai kolom pivot (can_view, can_create, can_update, can_delete, can_assign)
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

        if (!$user->role->relationLoaded('permissions')) {
            $user->role->load('permissions');
        }

        $menu = null;
        if (is_numeric($menuIdOrRoute)) {
            $menu = $user->role->permissions->firstWhere('id', (int) $menuIdOrRoute);
        } else {
            $menu = $user->role->permissions->firstWhere('slug', $menuIdOrRoute)
                ?? $user->role->permissions->firstWhere('route', $menuIdOrRoute);
        }

        if (!$menu) {
            return false;
        }

        $pivot = $menu->pivot;

        return (bool) ($pivot->can_view || $pivot->can_create || $pivot->can_update || $pivot->can_delete);
    }

    /**
     * Ambil menus untuk sidebar berdasarkan RBAC
     */
    public function getSidebarMenus(User $user)
    {
        if (!$user->role) {
            return collect();
        }

        // Superadmin lihat semua menu yang aktif
        if ($user->role->name === 'superadmin') {
            return Menu::whereNull('parent_id')
                ->where('is_active', true)
                ->with(['icon', 'children' => fn($q) => $q->where('is_active', true)])
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

        if (empty($viewableMenuIds)) {
            return collect();
        }

        // Query parent menus
        $menus = Menu::whereIn('id', $viewableMenuIds)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->with(['icon', 'children' => fn($q) => $q->where('is_active', true)])
            ->orderBy('position')
            ->get();

        // Filter children yang accessible
        return $menus->each(function ($menu) use ($viewableMenuIds) {
            $menu->children = $menu->children->filter(
                fn($child) => in_array($child->id, $viewableMenuIds)
            )->values();
        });
    }
}
