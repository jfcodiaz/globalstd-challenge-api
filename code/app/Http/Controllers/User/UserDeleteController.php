<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Routing\Controller;

class UserDeleteController extends Controller
{
    public function __invoke(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User successfully deleted (soft).',
        ]);
    }
}
