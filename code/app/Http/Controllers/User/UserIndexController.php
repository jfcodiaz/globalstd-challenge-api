<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Resources\User\UserCollectionResources;
use App\Repositories\UserRepository;

class UserIndexController extends Controller
{
    /**
     * List users with filters and pagination.
     *
     * Returns a paginated list of users. Supports filtering by search term, role and active status.
     *
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get list of users",
     *     description="Returns a paginated list of users. Supports search, role and active status filters.",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for name or email",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="john")
     *     ),
     *
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter by role name",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="Admin")
     *     ),
     *
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter by user status (1 = active, 0 = inactive)",
     *         required=false,
     *
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful list of users",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=73)
     *             )
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
     *     )
     * )
     */
    public function __invoke(
        UserIndexRequest $request, UserRepository $repository
    ): UserCollectionResources {

        $perPage = (int) $request->input('per_page', 15);
        $search = $request->input('search');
        $role = $request->input('role');
        $isActive = $request->input('is_active');

        $users = $repository->paginateWithFilters($search, $perPage, $role, $isActive);

        return new UserCollectionResources($users);
    }
}
