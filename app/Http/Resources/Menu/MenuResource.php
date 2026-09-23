<?php

namespace App\Http\Resources\Menu;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'icon_id' => $this->icon_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'route' => $this->route,
            'position' => $this->position,
            'is_active' => (bool) $this->is_active,
            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id' => $this->parent->id,
                    'name' => $this->parent->name,
                ];
            }),
            'icon' => $this->whenLoaded('icon', function () {
                return [
                    'id' => $this->icon->id,
                    'name' => $this->icon->name,
                    'class_name' => $this->icon->class_name,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
