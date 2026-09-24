<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrganizationSocialProfiles;
use App\Services\PermissionService;

class OrganizationSocialProfilePolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'company', 'view');
    }

    public function view(User $user, OrganizationSocialProfiles $profile): bool
    {
        return $this->permissionService->hasPermission($user, 'company', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'company', 'create');
    }

    public function update(User $user, OrganizationSocialProfiles $profile): bool
    {
        return $this->permissionService->hasPermission($user, 'company', 'update');
    }

    public function delete(User $user, OrganizationSocialProfiles $profile): bool
    {
        return $this->permissionService->hasPermission($user, 'company', 'delete');
    }
}
