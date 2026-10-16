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
        return $this->hasContactPermission($user, 'view');
    }

    public function view(User $user, OrganizationContact $contact): bool
    {
        return $this->hasContactPermission($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->hasContactPermission($user, 'create');
    }

    public function update(User $user, OrganizationContact $contact): bool
    {
        return $this->hasContactPermission($user, 'update');
    }

    public function delete(User $user, OrganizationContact $contact): bool
    {
        return $this->hasContactPermission($user, 'delete');
    }

    private function hasContactPermission(User $user, string $action): bool
    {
        return $this->permissionService->hasPermission($user, 'organization_contacts', $action)
            || $this->permissionService->hasPermission($user, 'organization_contact', $action)
            || $this->permissionService->hasPermission($user, 'pic', $action)
            || $this->permissionService->hasPermission($user, 'organization', $action);
    }
}
