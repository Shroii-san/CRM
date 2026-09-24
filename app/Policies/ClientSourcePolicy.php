<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClientSource;

class ClientSourcePolicy
{
    /**
     * Semua user terautentikasi dapat melihat daftar sumber client (untuk dropdown form).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ClientSource $source): bool
    {
        return true;
    }

    /**
     * Pembuatan sumber client khusus superadmin.
     */
    public function create(User $user): bool
    {
        return $user->role?->name === 'superadmin';
    }

    /**
     * Pembaharuan sumber client khusus superadmin.
     */
    public function update(User $user, ClientSource $source): bool
    {
        return $user->role?->name === 'superadmin';
    }

    /**
     * Penghapusan sumber client khusus superadmin.
     */
    public function delete(User $user, ClientSource $source): bool
    {
        return $user->role?->name === 'superadmin';
    }
}
