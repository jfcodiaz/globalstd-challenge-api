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

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    public function paginateWithFilters(?string $search, int $perPage = 15, ?string $role = null, ?int $isActive = null)
    {
        return User::query()
            ->with(['roles', 'avatar'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhereHas('roles', function ($r) use ($search) {
                            $r->where('name', 'ILIKE', "%{$search}%");
                        });
                });
            })
            ->when($role, function ($query, $role) {
                $query->whereHas('roles', function ($r) use ($role) {
                    $r->where('name', $role);
                });
            })
            ->when($isActive !== null, function ($query) use ($isActive) {
                $query->where('is_active', $isActive === 1);
            })
            ->paginate($perPage);
    }
}
