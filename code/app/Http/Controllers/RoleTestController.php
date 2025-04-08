<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleTestController extends Controller
{
    /**
     * Get authenticated user's name and roles.
     *
     * This endpoint is used to test role-based access control by returning the authenticated user's name and roles.
     *
     * @OA\Get(
     *     path="/api/test/superadmin",
     *     summary="Test access for SuperAdmin",
     *     description="Returns user name and roles. Requires SuperAdmin role.",
     *     operationId="testSuperAdminAccess",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Role access granted",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *
     *                 @OA\Items(type="string", example="SuperAdmin")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - insufficient role")
     * )
     *
     * @OA\Get(
     *     path="/api/test/admin",
     *     summary="Test access for Admin",
     *     description="Returns user name and roles. Requires Admin role.",
     *     operationId="testAdminAccess",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(response=200, description="Same as above"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - insufficient role")
     * )
     *
     * @OA\Get(
     *     path="/api/test/employee",
     *     summary="Test access for Employee",
     *     description="Returns user name and roles. Requires Employee role.",
     *     operationId="testEmployeeAccess",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(response=200, description="Same as above"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - insufficient role")
     * )
     *
     * @OA\Get(
     *     path="/api/test/client",
     *     summary="Test access for Client",
     *     description="Returns user name and roles. Requires Client role.",
     *     operationId="testClientAccess",
     *     tags={"Roles"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(response=200, description="Same as above"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - insufficient role")
     * )
     */
    public function show(Request $request)
    {
        return response()->json([
            'name' => $request->user()->name,
            'roles' => $request->user()->roles->pluck('name'),
        ]);
    }
}
