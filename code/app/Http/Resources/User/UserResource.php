<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Role\RoleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'avatar_id' => $this->avatar_id,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->created_at,
        ];
    }
}
