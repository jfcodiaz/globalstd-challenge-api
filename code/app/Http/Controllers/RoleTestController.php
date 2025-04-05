<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleTestController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'name' => $request->user()->name,
            'roles' => $request->user()->roles->pluck('name'),
        ]);
    }
}
