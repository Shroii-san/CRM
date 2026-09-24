<?php

namespace App\Http\Requests\Industry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIndustryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $industryId = $this->route('id') ?? $this->route('industry');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('industries', 'name')->ignore($industryId),
            ],
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
