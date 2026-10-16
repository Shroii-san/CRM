<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Services\PermissionService;

class TaskPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'tasks', 'view');
    }

    public function view(User $user, Task $task): bool
    {
        return $this->permissionService->hasPermission($user, 'tasks', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'tasks', 'create');
    }

    public function update(User $user, Task $task): bool
    {
        return $this->permissionService->hasPermission($user, 'tasks', 'update');
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->permissionService->hasPermission($user, 'tasks', 'delete');
    }
}
