<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use App\Services\PermissionService;

class AttachmentPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'attachments', 'view')
            || $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function view(User $user, Attachment $attachment): bool
    {
        return $this->permissionService->hasPermission($user, 'attachments', 'view')
            || $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'attachments', 'create')
            || $this->permissionService->hasPermission($user, 'customers', 'create');
    }

    public function update(User $user, Attachment $attachment): bool
    {
        return $this->permissionService->hasPermission($user, 'attachments', 'update')
            || $this->permissionService->hasPermission($user, 'customers', 'update');
    }

    public function delete(User $user, Attachment $attachment): bool
    {
        return $this->permissionService->hasPermission($user, 'attachments', 'delete')
            || $this->permissionService->hasPermission($user, 'customers', 'delete');
    }
}
