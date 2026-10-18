<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '-',
            'email' => $this->email ?? '-',
            'phone' => $this->phone ?? '-',
            'role' => $this->role?->name ?? 'No Role',
            'is_active' => $this->is_active ? 'Active' : 'Inactive',
        ];
    }
}
