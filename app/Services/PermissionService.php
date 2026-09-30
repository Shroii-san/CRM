<?php

namespace App\Services;

use App\Models\User;
use App\Models\Menu;

class PermissionService
{
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
