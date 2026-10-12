<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orgId = $this->route('id') ?? $this->route('organization');

        return [
            'industry_id' => 'required|exists:industries,id',
            'tier'        => 'nullable|string|max:1|in:A,B,C,D',
            'name'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('organizations', 'name')->ignore($orgId),
            ],
            'email'       => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('organizations', 'email')->ignore($orgId),
            ],
            'phone'       => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('organizations', 'phone')->ignore($orgId),
            ],
            'website'     => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'address'     => 'nullable|string',
            'province_id' => 'nullable|exists:provinces,id',
            'regency_id'  => 'nullable|exists:regencies,id',
            'district_id' => 'nullable|exists:districts,id',
            'village_id'  => 'nullable|exists:villages,id',
            'is_active'   => 'nullable|boolean',
        ];
    }
}
