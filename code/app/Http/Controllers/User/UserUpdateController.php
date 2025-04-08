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
    /**
     * Update an existing user.
     *
     * Allows Admin or SuperAdmin to update user data, including assigning roles.
     *
     * @OA\Patch(
     *     path="/api/users/{user}",
     *     summary="Update user",
     *     description="Updates an existing user. Only SuperAdmin and Admin may assign roles.",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="The ID of the user to update",
     *
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="Jane Updated"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@newmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newPassword123"),
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *
     *                 @OA\Items(type="string", example="Employee"),
     *                 description="Optional list of roles to assign (SuperAdmin/Admin only)"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=3),
     *                 @OA\Property(property="name", type="string", example="Jane Updated"),
     *                 @OA\Property(property="email", type="string", example="jane@newmail.com"),
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
     *                 ),
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="object",
     *                     nullable=true,
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="path", type="string", example="uploads/avatar.png")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - insufficient role"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=404, description="User not found")
     * )
     */
    public function __invoke(UserUpdateRequest $request, User $user, UserRepository $repository): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $repository->update($user, $data);

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
