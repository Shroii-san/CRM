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
        return $this->hasProfilePermission($user, 'view');
    }

    public function view(User $user, OrganizationSocialProfiles $profile): bool
    {
        return $this->hasProfilePermission($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->hasProfilePermission($user, 'create');
    }

    public function update(User $user, OrganizationSocialProfiles $profile): bool
    {
        return $this->hasProfilePermission($user, 'update');
    }

    public function delete(User $user, OrganizationSocialProfiles $profile): bool
    {
        return $this->hasProfilePermission($user, 'delete');
    }

    private function hasProfilePermission(User $user, string $action): bool
    {
        return $this->permissionService->hasPermission($user, 'organization_social_profiles', $action)
            || $this->permissionService->hasPermission($user, 'organization_social_profile', $action)
            || $this->permissionService->hasPermission($user, 'organization', $action)
            || $this->permissionService->hasPermission($user, 'company', $action);
    }
}
