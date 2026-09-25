<?php

namespace App\Http\Requests\Pipeline;

use Illuminate\Foundation\Http\FormRequest;

class StorePipelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:50|unique:pipelines,name',
            'description' => 'nullable|string|max:255',
            'is_active'   => 'nullable|boolean',
        ];
    }
}
