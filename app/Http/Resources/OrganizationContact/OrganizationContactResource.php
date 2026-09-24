<?php

namespace App\Http\Resources\OrganizationContact;

use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationContactResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'person_id' => $this->person_id,
            'job_title' => $this->job_title,
            'is_primary' => (bool) $this->is_primary,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'person' => $this->whenLoaded('person', function () {
                return [
                    'id' => $this->person->id,
                    'name' => $this->person->name,
                    'email' => $this->person->email,
                    'phone' => $this->person->phone,
                ];
            }),
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
