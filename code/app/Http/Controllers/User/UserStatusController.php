<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UserStatusRequest;
use App\Models\User;
use Illuminate\Routing\Controller;

class UserStatusController extends Controller
{
    /**
     * Update a user's active status.
     *
     * Allows SuperAdmin or Admin to activate or deactivate a user account.
     *
     * @OA\Patch(
     *     path="/api/users/{user}/status",
     *     summary="Update user status",
     *     description="Activate or deactivate a user account",
     *     operationId="updateUserStatus",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="The ID of the user whose status will be updated",
     *
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"status"},
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"active", "inactive"},
     *                 example="inactive",
     *                 description="New status for the user"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User status updated",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="User status updated to inactive.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - insufficient role"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function __invoke(UserStatusRequest $request, User $user)
    {
        if (in_array($request->status, ['active', 'inactive'])) {
            $user->is_active = $request->status === 'active';
            $user->save();
        }

        return response()->json([
            'message' => "User status updated to {$request->status}.",
        ]);
    }
}
