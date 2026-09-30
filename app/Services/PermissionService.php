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
        if ($user->role?->name === 'superadmin') {
            return true;
        }

        if (!$user->role) {
            return false;
        }

        $normalizedAction = match ($action) {
            'edit' => 'update',
            default => $action,
        };

        if (!$user->role->relationLoaded('permissions')) {
            $user->role->load('permissions');
        }

        $menu = is_numeric($menuIdOrRoute)
            ? $user->role->permissions->firstWhere('id', (int) $menuIdOrRoute)
            : $user->role->permissions->firstWhere('slug', $menuIdOrRoute);

        if (!$menu) {
            return false;
        }

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

        if (empty($viewableMenuIds)) {
            return collect();
        }

        // Query parent menus
        $menus = Menu::whereIn('id', $viewableMenuIds)
            ->parentOnly()
            ->where('is_active', true)
            ->with(['icon', 'children'])
            ->orderBy('position')
            ->get();

        // Filter children yang accessible
        return $menus->each(function ($menu) use ($viewableMenuIds) {
            if ($menu->children && $menu->children->count() > 0) {
                $filteredChildren = $menu->children
                    ->filter(fn($child) => in_array($child->id, $viewableMenuIds) && $child->is_active)
                    ->values();

                $menu->children = $filteredChildren;
            }
        });
    }
}
