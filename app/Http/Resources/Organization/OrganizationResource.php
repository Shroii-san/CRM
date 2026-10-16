<?php

namespace App\Http\Resources\Organization;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'industry_id' => $this->industry_id,
            'tier' => $this->tier,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'description' => $this->description,
            'address' => $this->address,
            'province_id' => $this->province_id,
            'regency_id' => $this->regency_id,
            'district_id' => $this->district_id,
            'village_id' => $this->village_id,
            'is_active' => (bool) $this->is_active,
            'industry' => $this->whenLoaded('industry', function () {
                return [
                    'id' => $this->industry->id,
                    'name' => $this->industry->name,
                ];
            }),
            'province' => $this->whenLoaded('province', function () {
                return [
                    'id' => $this->province->id,
                    'name' => $this->province->name,
                ];
            }),
            'regency' => $this->whenLoaded('regency', function () {
                return [
                    'id' => $this->regency->id,
                    'name' => $this->regency->name,
                ];
            }),
            'district' => $this->whenLoaded('district', function () {
                return [
                    'id' => $this->district->id,
                    'name' => $this->district->name,
                ];
            }),
            'village' => $this->whenLoaded('village', function () {
                return [
                    'id' => $this->village->id,
                    'name' => $this->village->name,
                ];
            }),
            'contacts' => $this->whenLoaded('contacts', function () {
                return $this->contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'name' => $contact->person?->name ?? '-',
                        'role' => $contact->job_title ?? '-',
                        'phone' => $contact->person?->phone ?? '-',
                        'email' => $contact->person?->email ?? '-',
                        'is_primary' => (bool) $contact->is_primary,
                    ];
                });
            }),
            'social_profiles' => $this->whenLoaded('socialProfiles', function () {
                return $this->socialProfiles->map(function ($profile) {
                    return [
                        'id' => $profile->id,
                        'platform' => $profile->platform,
                        'url' => $profile->url,
                        'username' => $profile->username,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
