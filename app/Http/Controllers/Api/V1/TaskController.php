<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskReminderRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskReminderResource;
use App\Http\Resources\Task\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
    }

    /**
     * Daftar tugas (JSON API).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $clientId = $request->input('client_id') ? (int) $request->input('client_id') : null;
        $dealId = $request->input('deal_id') ? (int) $request->input('deal_id') : null;
        $assignedUserId = $request->input('assigned_user_id') ? (int) $request->input('assigned_user_id') : null;
        $status = $request->has('status') ? (int) $request->input('status') : null;
        $search = $request->input('search');
        $perPage = (int) $request->input('per_page', 50);
        $page = (int) $request->input('page', 1);

        $tasks = $this->taskService->list(
            user: $user,
            clientId: $clientId,
            dealId: $dealId,
            assignedUserId: $assignedUserId,
            status: $status,
            search: $search,
            perPage: $perPage,
            page: $page
        );

        return TaskResource::collection($tasks)
            ->additional([
                'meta' => [
                    'total'        => $tasks->total(),
                    'per_page'     => $tasks->perPage(),
                    'current_page' => $tasks->currentPage(),
                    'last_page'    => $tasks->lastPage(),
                ]
            ])
            ->response();
    }

    /**
     * Membuat tugas baru.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->validated(), $request->user());

        return response()->json([
            'message' => 'Task created successfully',
            'data'    => TaskResource::make($task)
        ], 201);
    }

    /**
     * Detail tugas.
     */
    public function show(int|string $id): JsonResponse
    {
        $task = $this->taskService->find((int) $id);

        return response()->json([
            'data' => TaskResource::make($task)
        ]);
    }

    /**
     * Memperbarui tugas.
     */
    public function update(UpdateTaskRequest $request, int|string $id): JsonResponse
    {
        $task = $this->taskService->update((int) $id, $request->validated());

        return response()->json([
            'message' => 'Task updated successfully',
            'data'    => TaskResource::make($task)
        ]);
    }

    /**
     * Ubah status penyelesaian tugas (Complete / Uncomplete).
     */
    public function toggleComplete(Request $request, int|string $id): JsonResponse
    {
        $completed = $request->boolean('completed', true);
        $task = $this->taskService->toggleComplete((int) $id, $completed);

        return response()->json([
            'message' => $completed ? 'Task marked as completed' : 'Task marked as pending',
            'data'    => TaskResource::make($task)
        ]);
    }

    /**
     * Menghapus tugas.
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->taskService->delete((int) $id);

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Menambahkan reminder pada tugas.
     */
    public function addReminder(StoreTaskReminderRequest $request, int|string $id): JsonResponse
    {
        $reminder = $this->taskService->addReminder((int) $id, $request->validated());

        return response()->json([
            'message' => 'Task reminder added successfully',
            'data'    => TaskReminderResource::make($reminder)
        ], 201);
    }
}
