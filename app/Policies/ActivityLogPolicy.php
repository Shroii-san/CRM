<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\PermissionService;

class ActivityLogPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin()
            || $this->permissionService->hasPermission($user, 'activity_logs', 'view');
    }

    public function view(User $user, ActivityLog $log): bool
    {
        return $user->isSuperAdmin()
            || $this->permissionService->hasPermission($user, 'activity_logs', 'view');
    }
}
