<?php

namespace App\Http\Requests\OrganizationSocialProfile;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationSocialProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => 'required|exists:organizations,id',
            'platform'        => 'required|string|max:50',
            'username'        => 'required|string|max:255',
            'url'             => 'nullable|url|max:255|unique:organization_social_profiles,url',
        ];
    }
}
