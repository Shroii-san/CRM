<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Industry;

class IndustryPolicy
{
    /**
     * Semua user terautentikasi dapat melihat daftar industri (untuk dropdown form).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Industry $industry): bool
    {
        return true;
    }

    /**
     * Pembuatan industri khusus superadmin.
     */
    public function create(User $user): bool
    {
        return $user->role?->name === 'superadmin';
    }

    /**
     * Pembaharuan industri khusus superadmin.
     */
    public function update(User $user, Industry $industry): bool
    {
        return $user->role?->name === 'superadmin';
    }

    /**
     * Penghapusan industri khusus superadmin.
     */
    public function delete(User $user, Industry $industry): bool
    {
        return $user->role?->name === 'superadmin';
    }
}
