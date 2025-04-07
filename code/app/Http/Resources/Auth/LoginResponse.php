<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class LoginResponse extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'token' => $this->resource['token'],
            'user' => [
                'id' => $this->resource['user']->id,
                'name' => $this->resource['user']->name,
                'email' => $this->resource['user']->email,
                'is_active' => $this->resource['user']->is_active,
                'avatar_id' => $this->resource['user']->avatar_id,
                'roles' => $this->resource['user']->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                    ];
                }),
            ],
        ];
    }
}
