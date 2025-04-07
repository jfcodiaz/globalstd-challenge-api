<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResponse extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'avatar_id' => $this->avatar_id,
            'roles' => RoleResponse::collection($this->whenLoaded('roles')),
        ];
    }
}
