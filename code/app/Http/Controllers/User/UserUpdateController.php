<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserUpdateController extends Controller
{
    public function __invoke(UserUpdateRequest $request, User $user, UserRepository $repository): JsonResponse
    {
        $data = $request->validated();

        // Hash password si viene
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $repository->update($user, $data);

        // Solo SuperAdmin y Admin pueden asignar roles
        if (! empty($data['roles']) && $request->user()->hasAnyRole(['SuperAdmin', 'Admin'])) {
            $user->roles()->sync(
                \App\Models\Role::whereIn('name', $data['roles'])->pluck('id')
            );
        }

        return response()->json([
            'data' => $user->load('roles', 'avatar'),
        ]);
    }
}
