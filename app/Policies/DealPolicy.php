<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;
use App\Services\PermissionService;

class DealPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'transaksi', 'view')
            || $this->permissionService->hasPermission($user, 'deals', 'view');
    }

    public function view(User $user, Deal $deal): bool
    {
        return $this->permissionService->hasPermission($user, 'transaksi', 'view')
            || $this->permissionService->hasPermission($user, 'deals', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'transaksi', 'create')
            || $this->permissionService->hasPermission($user, 'deals', 'create');
    }

    public function update(User $user, Deal $deal): bool
    {
        return $this->permissionService->hasPermission($user, 'transaksi', 'update')
            || $this->permissionService->hasPermission($user, 'deals', 'update');
    }

    public function delete(User $user, Deal $deal): bool
    {
        return $this->permissionService->hasPermission($user, 'transaksi', 'delete')
            || $this->permissionService->hasPermission($user, 'deals', 'delete');
    }
}
