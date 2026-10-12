<?php

namespace App\Services;

use App\Models\StageTaskTemplate;
use App\Models\Task;
use App\Models\TaskReminder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TaskService
{
    /**
     * Mendapatkan daftar tugas dengan filter, search, sort, dan pagination.
     */
    public function list(
        ?User $user = null,
        ?int $clientId = null,
        ?int $dealId = null,
        ?int $assignedUserId = null,
        ?int $status = null,
        ?string $search = null,
        int $perPage = 50,
        int $page = 1,
        ?string $sortBy = 'due_at',
        ?string $sortDir = 'asc'
    ) {
        $params = array_filter([
            'client_id'        => $clientId,
            'deal_id'          => $dealId,
            'assigned_user_id' => $assignedUserId,
            'status'           => $status,
            'search'           => $search,
            'sort_by'          => $sortBy,
            'sort_dir'         => $sortDir,
        ], fn ($val) => $val !== null);

        return Task::query()
            ->with(['client', 'deal', 'taskTemplate', 'assignedUser', 'reminders'])
            ->applyFilters(
                params: $params,
                searchable: ['name', 'description'],
                filterable: ['client_id', 'deal_id', 'assigned_user_id', 'stage_task_template_id', 'status', 'priority'],
                sortable: ['id', 'name', 'due_at', 'completed_at', 'status', 'priority', 'created_at', 'updated_at'],
                defaultSortField: 'due_at',
                defaultSortDir: 'asc'
            )
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Detail tugas berdasarkan ID.
     */
    public function find(int $id): Task
    {
        return Task::with(['client', 'deal', 'taskTemplate', 'assignedUser', 'reminders'])->findOrFail($id);
    }

    /**
     * Membuat tugas baru.
     */
    public function create(array $data, User $creator): Task
    {
        return Task::create([
            'name'                   => $data['name'],
            'description'            => $data['description'] ?? null,
            'client_id'              => $data['client_id'] ?? null,
            'deal_id'                => $data['deal_id'] ?? null,
            'stage_task_template_id' => $data['stage_task_template_id'] ?? null,
            'assigned_user_id'       => $data['assigned_user_id'] ?? $creator->id,
            'due_at'                 => $data['due_at'] ?? null,
            'status'                 => $data['status'] ?? 1, // 1 = planned
            'priority'               => $data['priority'] ?? 1, // 1 = Low
        ]);
    }

    /**
     * Memperbarui tugas.
     */
    public function update(int $id, array $data): Task
    {
        $task = Task::findOrFail($id);

        $updateData = [];
        if (array_key_exists('name', $data))                   $updateData['name']                   = $data['name'];
        if (array_key_exists('description', $data))            $updateData['description']            = $data['description'];
        if (array_key_exists('client_id', $data))              $updateData['client_id']              = $data['client_id'];
        if (array_key_exists('deal_id', $data))                $updateData['deal_id']                = $data['deal_id'];
        if (array_key_exists('stage_task_template_id', $data)) $updateData['stage_task_template_id'] = $data['stage_task_template_id'];
        if (array_key_exists('assigned_user_id', $data))       $updateData['assigned_user_id']       = $data['assigned_user_id'];
        if (array_key_exists('due_at', $data))                 $updateData['due_at']                 = $data['due_at'];
        if (array_key_exists('completed_at', $data))           $updateData['completed_at']           = $data['completed_at'];
        if (array_key_exists('status', $data))                 $updateData['status']                 = (int) $data['status'];
        if (array_key_exists('priority', $data))               $updateData['priority']               = (int) $data['priority'];

        if (!empty($updateData)) {
            $task->update($updateData);
        }

        return $task->refresh();
    }

    /**
     * Tandai tugas sebagai selesai atau belum selesai.
     */
    public function toggleComplete(int $id, bool $completed = true): Task
    {
        $task = Task::findOrFail($id);
        $task->update([
            'status'       => $completed ? 3 : 1, // 3 = completed, 1 = planned
            'completed_at' => $completed ? now() : null,
        ]);

        return $task->refresh();
    }

    /**
     * Menghapus tugas.
     */
    public function delete(int $id): void
    {
        $task = Task::findOrFail($id);
        $task->delete();
    }

    /**
     * Menambahkan reminder untuk tugas.
     */
    public function addReminder(int $taskId, array $data): TaskReminder
    {
        $task = Task::findOrFail($taskId);

        return TaskReminder::create([
            'task_id'   => $task->id,
            'remind_at' => $data['remind_at'],
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Membuat tugas-tugas otomatis berdasarkan template tugas stage untuk deal baru.
     */
    public function createFromStageTemplates(int $dealId, int $stageId, int $assignedUserId): void
    {
        $templates = StageTaskTemplate::where('stage_id', $stageId)
            ->where('is_active', true)
            ->get();

        foreach ($templates as $template) {
            $dueAt = $template->due_offset_days !== null
                ? now()->addDays($template->due_offset_days)->toDateString()
                : null;

            Task::create([
                'name'                   => $template->name,
                'description'            => $template->description,
                'deal_id'                => $dealId,
                'stage_task_template_id' => $template->id,
                'assigned_user_id'       => $assignedUserId,
                'due_at'                 => $dueAt,
                'status'                 => 1, // planned
                'priority'               => $template->priority ?? 1,
            ]);
        }
    }
}
