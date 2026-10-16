<?php

namespace App\Http\Requests\Pipeline;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePipelineStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'nullable|string|max:50',
            'slug'        => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'position'    => 'nullable|integer',
            'is_terminal' => 'nullable|boolean',
            'is_active'   => 'nullable|boolean',
        ];
    }
}
