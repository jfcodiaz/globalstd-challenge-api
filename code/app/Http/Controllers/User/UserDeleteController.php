<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Routing\Controller;

class UserDeleteController extends Controller
{
    /**
     * Soft delete a user.
     *
     * Marks a user as deleted (soft delete), accessible only to Admin or SuperAdmin roles.
     *
     * @OA\Delete(
     *     path="/api/users/{user}",
     *     summary="Delete user (soft)",
     *     description="Marks a user as deleted without removing them permanently from the database.",
     *     operationId="deleteUser",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="The ID of the user to delete",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User successfully soft deleted",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="User successfully deleted (soft).")
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
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function __invoke(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User successfully deleted (soft).',
        ]);
    }
}
