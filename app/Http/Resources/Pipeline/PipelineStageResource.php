<?php

namespace App\Http\Resources\Pipeline;

use Illuminate\Http\Resources\Json\JsonResource;

class PipelineStageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'pipeline_id' => $this->pipeline_id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'description' => $this->description,
            'position'    => (int) $this->position,
            'is_terminal' => (bool) $this->is_terminal,
            'is_active'   => (bool) $this->is_active,
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
