<?php

namespace App\Policies;

use App\Models\User;
use App\Services\PermissionService;

class UserPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    /**
     * Memeriksa apakah user dapat melihat daftar user.
     */
    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'users', 'view');
    }

    /**
     * Memeriksa apakah user dapat melihat detail user.
     */
    public function view(User $user, User $model): bool
    {
        return $this->permissionService->hasPermission($user, 'users', 'view');
    }

    /**
     * Memeriksa apakah user dapat membuat user baru.
     */
    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'users', 'create');
    }

    /**
     * Memeriksa apakah user dapat mengedit user.
     */
    public function update(User $user, User $model): bool
    {
        return $this->permissionService->hasPermission($user, 'users', 'update');
    }

    /**
     * Memeriksa apakah user dapat menghapus user.
     */
    public function delete(User $user, User $model): bool
    {
        return $this->permissionService->hasPermission($user, 'users', 'delete');
    }
}
