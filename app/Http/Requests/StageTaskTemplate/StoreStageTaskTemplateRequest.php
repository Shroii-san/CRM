<?php

namespace App\Http\Requests\StageTaskTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreStageTaskTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stage_id'        => 'required|exists:pipeline_stages,id',
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string|max:255',
            'priority'        => 'nullable|integer',
            'due_offset_days' => 'nullable|integer',
            'is_required'     => 'nullable|boolean',
            'is_active'       => 'nullable|boolean',
        ];
    }
}
