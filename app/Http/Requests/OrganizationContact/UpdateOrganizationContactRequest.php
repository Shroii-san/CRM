<?php

namespace App\Http\Requests\OrganizationContact;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'nullable|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:20',
            'job_title'  => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'started_at' => 'nullable|date',
            'ended_at'   => 'nullable|date|after:started_at',
        ];
    }
}
