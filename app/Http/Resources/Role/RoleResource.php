<?php

namespace App\Http\Resources\Role;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->map(function ($menu) {
                    return [
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'menu_route' => $menu->route,
                        'can_view' => (bool) $menu->pivot->can_view,
                        'can_create' => (bool) $menu->pivot->can_create,
                        'can_update' => (bool) $menu->pivot->can_update,
                        'can_delete' => (bool) $menu->pivot->can_delete,
                        'can_assign' => (bool) $menu->pivot->can_assign,
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
