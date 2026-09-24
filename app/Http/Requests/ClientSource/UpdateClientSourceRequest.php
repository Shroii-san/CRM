<?php

namespace App\Http\Requests\ClientSource;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sourceId = $this->route('id') ?? $this->route('client_source');

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('client_sources', 'name')->ignore($sourceId),
            ],
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
