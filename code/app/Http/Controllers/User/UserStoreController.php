<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class UserStoreController extends Controller
{
    public function __invoke(UserStoreRequest $request, UserRepository $userRepository ): JsonResponse
    {
        $data = $request->validated();

        $user = $userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if (!empty($data['roles'])) {
            $user->assignRoles($data['roles']);
        }

        return response()->json([
            'data' => $user->load('roles', 'avatar'),
        ], 201);
    }
}
