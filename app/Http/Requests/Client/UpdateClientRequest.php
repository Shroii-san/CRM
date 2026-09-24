<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'person_id'       => 'nullable|exists:persons,id',
            'organization_id' => 'nullable|exists:organizations,id',
            'source_id'       => 'required|exists:client_sources,id',
            'is_active'       => 'nullable|boolean',
        ];
    }
}
