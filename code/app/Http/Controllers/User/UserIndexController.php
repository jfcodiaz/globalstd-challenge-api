<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserIndexRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

class UserIndexController extends Controller
{
    public function __invoke(UserIndexRequest $request, UserRepository $repository): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $search = $request->input('search');

        $users = $repository->paginateWithFilters($search, $perPage);

        return response()->json($users);
    }
}
