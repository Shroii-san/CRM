<?php

namespace App\Http\Resources\Deal;

use App\Http\Resources\Pipeline\PipelineStageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DealStageHistoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'deal_id'       => $this->deal_id,
            'from_stage_id' => $this->from_stage_id,
            'to_stage_id'   => $this->to_stage_id,
            'changed_by'    => $this->changed_by,
            'from_stage'    => PipelineStageResource::make($this->whenLoaded('fromStage')),
            'to_stage'      => PipelineStageResource::make($this->whenLoaded('toStage')),
            'user'          => $this->whenLoaded('changedBy', function () {
                return [
                    'id'    => $this->changedBy->id,
                    'name'  => $this->changedBy->name,
                    'email' => $this->changedBy->email,
                ];
            }),
            'changed_at'    => $this->changed_at?->toIso8601String(),
        ];
    }
}
