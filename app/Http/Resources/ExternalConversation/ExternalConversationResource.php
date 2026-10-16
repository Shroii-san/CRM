<?php

namespace App\Http\Resources\ExternalConversation;

use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\Client\ClientResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ExternalConversationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                       => $this->id,
            'platform'                 => $this->platform,
            'external_conversation_id' => $this->external_conversation_id,
            'client_id'                => $this->client_id,
            'last_interaction_at'      => $this->last_interaction_at?->toIso8601String(),
            'metadata'                 => $this->metadata,
            'client'                   => ClientResource::make($this->whenLoaded('client')),
            'attachments'              => AttachmentResource::collection($this->whenLoaded('attachable')),
            'created_at'               => $this->created_at?->toIso8601String(),
            'updated_at'               => $this->updated_at?->toIso8601String(),
        ];
    }
}
