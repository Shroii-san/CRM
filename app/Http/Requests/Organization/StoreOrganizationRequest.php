<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'industry_id' => 'required|exists:industries,id',
            'tier'        => 'nullable|string|max:1|in:A,B,C,D',
            'name'        => 'required|string|max:255|unique:organizations,name',
            'email'       => 'nullable|email|max:255|unique:organizations,email',
            'phone'       => 'nullable|string|max:20|unique:organizations,phone',
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
