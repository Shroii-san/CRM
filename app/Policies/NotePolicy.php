<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use App\Services\PermissionService;

class NotePolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'notes', 'view')
            || $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function view(User $user, Note $note): bool
    {
        return $this->permissionService->hasPermission($user, 'notes', 'view')
            || $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'notes', 'create')
            || $this->permissionService->hasPermission($user, 'customers', 'create');
    }

    public function update(User $user, Note $note): bool
    {
        return $this->permissionService->hasPermission($user, 'notes', 'update')
            || $this->permissionService->hasPermission($user, 'customers', 'update');
    }

    public function delete(User $user, Note $note): bool
    {
        return $this->permissionService->hasPermission($user, 'notes', 'delete')
            || $this->permissionService->hasPermission($user, 'customers', 'delete');
    }
}
