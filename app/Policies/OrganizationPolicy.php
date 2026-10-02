<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Organization;
use App\Services\PermissionService;

class OrganizationPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->hasOrganizationPermission($user, 'view');
    }

    public function view(User $user, Organization $organization): bool
    {
        return $this->hasOrganizationPermission($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->hasOrganizationPermission($user, 'create');
    }

    public function update(User $user, Organization $organization): bool
    {
        return $this->hasOrganizationPermission($user, 'update');
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $this->hasOrganizationPermission($user, 'delete');
    }

    private function hasOrganizationPermission(User $user, string $action): bool
    {
        return $this->permissionService->hasPermission($user, 'organization', $action)
            || $this->permissionService->hasPermission($user, 'organizations', $action)
            || $this->permissionService->hasPermission($user, 'company', $action)
            || $this->permissionService->hasPermission($user, 'companies', $action);
    }
}
