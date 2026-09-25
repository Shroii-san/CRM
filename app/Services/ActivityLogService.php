<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Mendapatkan daftar log audit aktivitas dengan filter dan pagination.
     */
    public function list(
        ?User $user = null,
        ?int $filterUserId = null,
        ?string $search = null,
        ?string $action = null,
        ?string $targetType = null,
        ?int $targetId = null,
        int $perPage = 50,
        int $page = 1
    ) {
        $query = ActivityLog::query()
            ->with(['user', 'target'])
            ->orderBy('created_at', 'desc');

        if ($filterUserId) {
            $query->where('user_id', $filterUserId);
        }

        if ($action) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($targetType) {
            $query->where('target_type', $targetType);
        }

        if ($targetId) {
            $query->where('target_id', $targetId);
        }

        if ($search) {
            $query->where('action', 'like', "%{$search}%");
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Detail log audit berdasarkan ID.
     */
    public function find(int $id): ActivityLog
    {
        return ActivityLog::with(['user', 'target'])->findOrFail($id);
    }

    /**
     * Catat log aktivitas baru secara manual.
     */
    public function log(string $action, mixed $target = null, ?array $metadata = null, ?int $userId = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id'     => $userId ?? Auth::id(),
            'action'      => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id'   => $target ? $target->getKey() : null,
            'metadata'    => $metadata,
        ]);
    }
}
