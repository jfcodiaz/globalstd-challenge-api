<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UserAvatarAssignRequest;
use App\Models\Media;
use Illuminate\Routing\Controller;

class UserAvatarAssignController extends Controller
{
    public function __invoke(UserAvatarAssignRequest $request)
    {
        $user = $request->user();

        $media = Media::findOrFail($request->input('media_id'));
        $user->avatar_id = $media->id;
        $user->save();

        return response()->json(['message' => 'Avatar assigned successfully.']);
    }
}
