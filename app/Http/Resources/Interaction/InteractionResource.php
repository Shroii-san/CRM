<?php

namespace App\Http\Resources\Interaction;

use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Deal\DealResource;
use App\Http\Resources\OrganizationContact\OrganizationContactResource;
use Illuminate\Http\Resources\Json\JsonResource;

class InteractionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                      => $this->id,
            'client_id'               => $this->client_id,
            'deal_id'                => $this->deal_id,
            'organization_contact_id' => $this->organization_contact_id,
            'type'                    => (int) $this->type,
            'subject'                 => $this->subject,
            'description'            => $this->description,
            'summary'                 => $this->summary,
            'status'                  => (int) $this->status,
            'start_at'                => $this->start_at?->toIso8601String(),
            'end_at'                  => $this->end_at?->toIso8601String(),
            'performed_by'            => $this->performed_by,
            'external_reference'      => $this->external_reference,
            'client'                  => ClientResource::make($this->whenLoaded('client')),
            'deal'                    => DealResource::make($this->whenLoaded('deal')),
            'contact_person'          => OrganizationContactResource::make($this->whenLoaded('contactPerson')),
            'performer'               => $this->whenLoaded('performer', function () {
                return [
                    'id'    => $this->performer->id,
                    'name'  => $this->performer->name,
                    'email' => $this->performer->email,
                ];
            }),
            'created_at'              => $this->created_at?->toIso8601String(),
            'updated_at'              => $this->updated_at?->toIso8601String(),
        ];
    }
}
