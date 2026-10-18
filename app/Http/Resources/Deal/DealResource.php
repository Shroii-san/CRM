<?php

namespace App\Http\Resources\Deal;

use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Pipeline\PipelineResource;
use App\Http\Resources\Pipeline\PipelineStageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'client_id'          => $this->client_id,
            'pipeline_id'        => $this->pipeline_id,
            'current_stage_id'   => $this->current_stage_id,
            'assigned_user_id'   => $this->assigned_user_id,
            'description'        => $this->description,
            'currency'           => $this->currency,
            'value'              => (float) $this->value,
            'value_formatted'    => $this->value_formatted,
            'status'             => (int) $this->status,
            'priority'           => (int) $this->priority,
            'expected_closed_at' => $this->expected_closed_at?->format('Y-m-d'),
            'actual_closed_at'   => $this->actual_closed_at?->format('Y-m-d'),
            'is_active'          => (bool) $this->is_active,
            'client'             => ClientResource::make($this->whenLoaded('client')),
            'pipeline'           => PipelineResource::make($this->whenLoaded('pipeline')),
            'current_stage'      => PipelineStageResource::make($this->whenLoaded('currentStage')),
            'assigned_user'      => $this->whenLoaded('assignedUser', function () {
                return [
                    'id'    => $this->assignedUser->id,
                    'name'  => $this->assignedUser->name,
                    'email' => $this->assignedUser->email,
                ];
            }),
            'stage_histories'    => DealStageHistoryResource::collection($this->whenLoaded('dealStageHistories')),
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
