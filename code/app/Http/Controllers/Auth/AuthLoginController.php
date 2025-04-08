<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthLoginController extends Controller
{
    /**
     * Login to get an access token.
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     description="Authenticate a user and return an access token along with user information",
     *     operationId="authLogin",
     *     tags={"Authentication"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="user@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="password123"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 description="Access token for the authenticated user"
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Authenticated user data",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(
     *                     property="roles",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or inactive account"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function __invoke(AuthLoginRequest $request): LoginResource
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();
            $user = $user->load('roles')->setRelation(
                'roles',
                $user->roles->makeHidden('pivot')
            );
            if ($user->is_active === false) {
                throw ValidationException::withMessages([
                    'email' => ['Your account is not active.'],
                ]);
            }
            $token = $user->createToken('api-token')->plainTextToken;

            return new LoginResource([
                'token' => $token,
                'user' => $user,
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }
}
