<?php

namespace App\Http\Requests\ClientSource;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:client_sources,name',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
