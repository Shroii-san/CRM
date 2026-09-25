<?php

namespace App\Http\Resources\SalesVisit;

use App\Http\Resources\Organization\OrganizationResource;
use App\Http\Resources\OrganizationContact\OrganizationContactResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesVisitResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                      => $this->id,
            'user_id'                 => $this->user_id,
            'organization_id'         => $this->organization_id,
            'organization_contact_id' => $this->organization_contact_id,
            'attachment_id'           => $this->attachment_id,
            'visit_date'              => $this->visit_date?->format('Y-m-d'),
            'visit_purpose'           => $this->visit_purpose,
            'is_follow_up'            => (bool) $this->is_follow_up,
            'latitude'                => $this->latitude ? (float) $this->latitude : null,
            'longitude'               => $this->longitude ? (float) $this->longitude : null,
            'address'                 => $this->address,
            'province_id'             => $this->province_id,
            'regency_id'              => $this->regency_id,
            'district_id'             => $this->district_id,
            'village_id'              => $this->village_id,
            'organization'            => OrganizationResource::make($this->whenLoaded('organization')),
            'contact_person'          => OrganizationContactResource::make($this->whenLoaded('contactPerson')),
            'user'                    => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'created_at'              => $this->created_at?->toIso8601String(),
            'updated_at'              => $this->updated_at?->toIso8601String(),
        ];
    }
}
