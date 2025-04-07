<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Resources\User\UserCollectionResources;
use App\Repositories\UserRepository;

class UserIndexController extends Controller
{
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
