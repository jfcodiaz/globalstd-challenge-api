<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollectionResources extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'users' => UserResource::collection($this->collection),
        ];
    }
}
