<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'person_id' => $this->person_id,
            'organization_id' => $this->organization_id,
            'source_id' => $this->source_id,
            'is_active' => (bool) $this->is_active,
            'client_type' => $this->organization_id ? 'organization' : 'person',
            'name' => $this->organization ? $this->organization->name : ($this->person ? $this->person->name : '-'),
            'email' => $this->organization ? $this->organization->email : ($this->person ? $this->person->email : '-'),
            'phone' => $this->organization ? $this->organization->phone : ($this->person ? $this->person->phone : '-'),
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
                    'email' => $this->organization->email,
                    'phone' => $this->organization->phone,
                ];
            }),
            'source' => $this->whenLoaded('source', function () {
                return [
                    'id' => $this->source->id,
                    'name' => $this->source->name,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
