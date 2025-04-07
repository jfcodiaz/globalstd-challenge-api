<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UserStatusRequest;
use App\Models\User;
use Illuminate\Routing\Controller;

class UserStatusController extends Controller
{
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
