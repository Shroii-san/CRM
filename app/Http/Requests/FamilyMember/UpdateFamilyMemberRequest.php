<?php

namespace App\Http\Requests\FamilyMember;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFamilyMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'birth_place' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'integer', 'in:1,2,3'],
        ];
    }
}
