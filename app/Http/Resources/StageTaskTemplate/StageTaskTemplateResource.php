<?php

namespace App\Http\Resources\StageTaskTemplate;

use App\Http\Resources\Pipeline\PipelineStageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StageTaskTemplateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'stage_id'        => $this->stage_id,
            'name'            => $this->name,
            'description'     => $this->description,
            'priority'        => (int) $this->priority,
            'due_offset_days' => $this->due_offset_days ? (int) $this->due_offset_days : null,
            'is_required'     => (bool) $this->is_required,
            'is_active'       => (bool) $this->is_active,
            'stage'           => PipelineStageResource::make($this->whenLoaded('pipelineStage')),
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
