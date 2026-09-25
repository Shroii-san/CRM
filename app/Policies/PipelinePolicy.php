<?php

namespace App\Policies;

use App\Models\Pipeline;
use App\Models\User;
use App\Services\PermissionService;

class PipelinePolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'pipelines', 'view');
    }

    public function view(User $user, Pipeline $pipeline): bool
    {
        return $this->permissionService->hasPermission($user, 'pipelines', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'pipelines', 'create');
    }

    public function update(User $user, Pipeline $pipeline): bool
    {
        return $this->permissionService->hasPermission($user, 'pipelines', 'update');
    }

    public function delete(User $user, Pipeline $pipeline): bool
    {
        return $this->permissionService->hasPermission($user, 'pipelines', 'delete');
    }
}
