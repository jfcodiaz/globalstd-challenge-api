<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    protected string $modelClass = User::class;

    public function __construct(
        protected RoleRepository $roleRepository
    ) {}

    public function create(array $data, array $roles = ['Client']): User
    {
        /** @var User $user */
        $user = $this->firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]
        );

        $roles = array_unique(array_merge(['Client'], $roles));

        foreach ($roles as $role) {
            $this->roleRepository->assignToUser($role, $user);
        }

        return $user;
    }
}
