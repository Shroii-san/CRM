<?php

namespace App\Http\Requests\OrganizationSocialProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationSocialProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = $this->route('id') ?? $this->route('social_profile');

        return [
            'organization_id' => 'required|exists:organizations,id',
            'platform'        => 'required|string|max:50',
            'username'        => 'required|string|max:255',
            'url'             => [
                'nullable',
                'url',
                'max:255',
                Rule::unique('organization_social_profiles', 'url')->ignore($profileId),
            ],
        ];
    }
}
