<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrganizationContact;
use App\Services\PermissionService;

class OrganizationContactPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'pic', 'view');
    }

    public function view(User $user, OrganizationContact $contact): bool
    {
        return $this->permissionService->hasPermission($user, 'pic', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'pic', 'create');
    }

    public function update(User $user, OrganizationContact $contact): bool
    {
        return $this->permissionService->hasPermission($user, 'pic', 'update');
    }

    public function delete(User $user, OrganizationContact $contact): bool
    {
        return $this->permissionService->hasPermission($user, 'pic', 'delete');
    }
}
