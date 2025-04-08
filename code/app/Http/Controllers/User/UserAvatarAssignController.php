<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UserAvatarAssignRequest;
use App\Models\Media;
use Illuminate\Routing\Controller;

class UserAvatarAssignController extends Controller
{
    /**
     * Assign an uploaded media file as the user's avatar.
     *
     * Requires a valid media ID that belongs to the authenticated user.
     *
     * @OA\Patch(
     *     path="/api/user/avatar",
     *     summary="Assign avatar to user",
     *     description="Assigns an uploaded media file as the authenticated user's avatar",
     *     operationId="assignUserAvatar",
     *     tags={"Avatar"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"media_id"},
     *
     *             @OA\Property(
     *                 property="media_id",
     *                 type="integer",
     *                 example=1,
     *                 description="The ID of the media file to assign as avatar"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Avatar assigned successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Avatar assigned successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Media not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function __invoke(UserAvatarAssignRequest $request)
    {
        $user = $request->user();

        $media = Media::findOrFail($request->input('media_id'));
        $user->avatar_id = $media->id;
        $user->save();

        return response()->json(['message' => 'Avatar assigned successfully.']);
    }
}
