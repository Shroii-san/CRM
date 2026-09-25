<?php

namespace App\Policies;

use App\Models\Interaction;
use App\Models\User;
use App\Services\PermissionService;

class InteractionPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'interactions', 'view')
            || $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function view(User $user, Interaction $interaction): bool
    {
        return $this->permissionService->hasPermission($user, 'interactions', 'view')
            || $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'interactions', 'create')
            || $this->permissionService->hasPermission($user, 'customers', 'create');
    }

    public function update(User $user, Interaction $interaction): bool
    {
        return $this->permissionService->hasPermission($user, 'interactions', 'update')
            || $this->permissionService->hasPermission($user, 'customers', 'update');
    }

    public function delete(User $user, Interaction $interaction): bool
    {
        return $this->permissionService->hasPermission($user, 'interactions', 'delete')
            || $this->permissionService->hasPermission($user, 'customers', 'delete');
    }
}
