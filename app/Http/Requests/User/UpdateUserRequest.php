<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('users', 'name')->ignore($userId),
            ],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|required|string|min:6|confirmed',
            'role_id' => 'sometimes|required|exists:roles,id',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
