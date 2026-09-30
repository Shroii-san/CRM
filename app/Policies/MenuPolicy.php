<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Menu;

class MenuPolicy
{
    /**
     * Hanya superadmin yang bisa akses menu management
     */
    public function viewAny(User $user): bool
    {
        return $user->role?->name === 'superadmin';
    }

    public function view(User $user, Menu $menu): bool
    {
        return $user->role?->name === 'superadmin';
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'superadmin';
    }

    public function update(User $user, Menu $menu): bool
    {
        return $user->role?->name === 'superadmin';
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $user->role?->name === 'superadmin';
    }
}
