<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_id'   => 'required|exists:tasks,id',
            'remind_at' => 'required|date',
            'is_active' => 'nullable|boolean',
        ];
    }
}
