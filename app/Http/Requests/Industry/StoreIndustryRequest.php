<?php

namespace App\Http\Requests\Industry;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndustryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:industries,name',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
