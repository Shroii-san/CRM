<?php

namespace App\Policies;

use App\Models\User;

class RegionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user): bool
    {
        return true;
    }
}
