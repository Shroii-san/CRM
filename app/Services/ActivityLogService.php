<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Mendapatkan daftar log audit aktivitas dengan filter, search, sort, dan pagination.
     */
    public function list(
        ?User $user = null,
        ?int $filterUserId = null,
        ?string $search = null,
        ?string $action = null,
        ?string $targetType = null,
        ?int $targetId = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'created_at',
        ?string $sortDir = 'desc'
    ) {
        $params = array_filter([
            'user_id'     => $filterUserId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'search'      => $search,
            'sort_by'     => $sortBy,
            'sort_dir'    => $sortDir,
        ], fn ($val) => $val !== null);

        return ActivityLog::query()
            ->with(['user', 'target'])
            ->applyFilters(
                params: $params,
                searchable: ['action', 'target_type'],
                filterable: ['user_id', 'action', 'target_type', 'target_id'],
                sortable: ['id', 'action', 'created_at'],
                defaultSortField: 'created_at',
                defaultSortDir: 'desc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
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
