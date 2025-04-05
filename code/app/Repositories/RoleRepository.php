<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository extends BaseRepository
{
    protected string $modelClass = Role::class;

    public function createBaseRoles(): void
    {
        foreach (['SuperAdmin', 'Admin', 'Employee', 'Client'] as $name) {
            $this->firstOrCreate(['name' => $name], [
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function getByName(string $name): ?Role
    {
        return Role::where('name', $name)->first();
    }

    public function assignToUser(Role|string $role, $user): void
    {
        $role = is_string($role) ? $this->getByName($role) : $role;

        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
