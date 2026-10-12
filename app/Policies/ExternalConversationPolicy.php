<?php

namespace App\Policies;

use App\Models\ExternalConversation;
use App\Models\User;
use App\Services\PermissionService;

class ExternalConversationPolicy
{
    public function __construct(private PermissionService $permissionService)
    {
    }

    public function viewAny(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'external_conversations', 'view')
            || $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function view(User $user, ExternalConversation $conversation): bool
    {
        return $this->permissionService->hasPermission($user, 'external_conversations', 'view')
            || $this->permissionService->hasPermission($user, 'customers', 'view');
    }

    public function create(User $user): bool
    {
        return $this->permissionService->hasPermission($user, 'external_conversations', 'create')
            || $this->permissionService->hasPermission($user, 'customers', 'create');
    }

    public function update(User $user, ExternalConversation $conversation): bool
    {
        return $this->permissionService->hasPermission($user, 'external_conversations', 'update')
            || $this->permissionService->hasPermission($user, 'customers', 'update');
    }

    public function delete(User $user, ExternalConversation $conversation): bool
    {
        return $this->permissionService->hasPermission($user, 'external_conversations', 'delete')
            || $this->permissionService->hasPermission($user, 'customers', 'delete');
    }
}
