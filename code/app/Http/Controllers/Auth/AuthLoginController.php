<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Resources\Auth\LoginResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthLoginController extends Controller
{
    public function __invoke(AuthLoginRequest $request): LoginResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();
            $user = $user->load('roles')->setRelation(
                'roles',
                $user->roles->makeHidden('pivot')
            );
            $token = $user->createToken('api-token')->plainTextToken;

            return new LoginResponse([
                'token' => $token,
                'user' => $user,
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
}
