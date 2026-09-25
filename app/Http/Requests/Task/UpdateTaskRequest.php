<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                   => 'nullable|string|max:255',
            'description'            => 'nullable|string',
            'client_id'              => 'nullable|exists:clients,id',
            'deal_id'                => 'nullable|exists:deals,id',
            'stage_task_template_id' => 'nullable|exists:stage_task_templates,id',
            'assigned_user_id'       => 'nullable|exists:users,id',
            'due_at'                 => 'nullable|date',
            'completed_at'           => 'nullable|date',
            'status'                 => 'nullable|integer',
            'priority'               => 'nullable|integer',
        ];
    }
}
