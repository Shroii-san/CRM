<?php

namespace App\Policies;

use App\Models\SalesVisit;
use App\Models\User;
use App\Services\PermissionService;

class SalesVisitPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'sales_visit', 'view');
    }

    public function view(User $user, SalesVisit $visit): bool
    {
        return $this->permissionService->hasPermission($user, 'sales_visit', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'sales_visit', 'create');
    }

    public function update(User $user, SalesVisit $visit): bool
    {
        return $this->permissionService->hasPermission($user, 'sales_visit', 'update');
    }

    public function delete(User $user, SalesVisit $visit): bool
    {
        return $this->permissionService->hasPermission($user, 'sales_visit', 'delete');
    }
}
