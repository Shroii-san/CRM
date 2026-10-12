<?php

namespace App\Http\Resources\Note;

use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Deal\DealResource;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'client_id'  => $this->client_id,
            'deal_id'    => $this->deal_id,
            'content'    => $this->content,
            'note_type'  => $this->note_type,
            'created_by' => $this->created_by,
            'client'     => ClientResource::make($this->whenLoaded('client')),
            'deal'       => DealResource::make($this->whenLoaded('deal')),
            'creator'    => $this->whenLoaded('createdBy', function () {
                return [
                    'id'    => $this->createdBy->id,
                    'name'  => $this->createdBy->name,
                    'email' => $this->createdBy->email,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
