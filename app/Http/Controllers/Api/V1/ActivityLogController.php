<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLog\ActivityLogResource;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(private ActivityLogService $logService)
    {
    }

    /**
     * Daftar audit activity log (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filterUserId = $request->input('user_id') ? (int) $request->input('user_id') : null;
        $action = $request->input('action');
        $targetType = $request->input('target_type');
        $targetId = $request->input('target_id') ? (int) $request->input('target_id') : null;
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $logs = $this->logService->list(
            user: $user,
            filterUserId: $filterUserId,
            action: $action,
            targetType: $targetType,
            targetId: $targetId,
            perPage: $perPage,
            page: $page
        );

        return $this->paginatedResponse($logs, ActivityLogResource::class, 'Activity log list retrieved successfully');
    }

    /**
     * Detail log audit.
     */
    public function show(int|string $id): JsonResponse
    {
        $log = $this->logService->find((int) $id);

        return $this->successResponse(
            ActivityLogResource::make($log),
            'Activity log detail retrieved successfully'
        );
    }
}
