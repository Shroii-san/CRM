<?php

namespace App\Http\Resources\ActivityLog;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'action'      => $this->action,
            'target_type' => $this->target_type,
            'target_id'   => $this->target_id,
            'metadata'    => $this->metadata,
            'user'        => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'target'      => $this->whenLoaded('target'),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
