<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;
use App\Services\PermissionService;

class ClientPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function view(User $user, Client $client): bool
    {
        return $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'customers', 'create');
    }

    public function update(User $user, Client $client): bool
    {
        return $this->permissionService->hasPermission($user, 'customers', 'update');
    }

    public function delete(User $user, Client $client): bool
    {
        return $this->permissionService->hasPermission($user, 'customers', 'delete');
    }
}
