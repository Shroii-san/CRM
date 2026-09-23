<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use App\Services\PermissionService;

class RolePolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'role', 'view');
    }

    public function view(User $user, Role $role): bool
    {
        return $this->permissionService->hasPermission($user, 'role', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'role', 'create');
    }

    public function update(User $user, Role $role): bool
    {
        return $this->permissionService->hasPermission($user, 'role', 'update');
    }

    public function delete(User $user, Role $role): bool
    {
        if ($role->name === 'superadmin') {
            return false;
        }

        return $this->permissionService->hasPermission($user, 'role', 'delete');
    }

    public function assignPermissions(User $user, Role $role): bool
    {
        return $this->permissionService->hasPermission($user, 'role', 'assign');
    }
}
