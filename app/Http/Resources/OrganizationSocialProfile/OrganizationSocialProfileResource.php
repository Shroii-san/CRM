<?php

namespace App\Http\Resources\OrganizationSocialProfile;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationSocialProfileResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'platform' => $this->platform,
            'username' => $this->username,
            'url' => $this->url,
            'organization' => $this->whenLoaded('organization', function () {
                return [
                    'id' => $this->organization->id,
                    'name' => $this->organization->name,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
