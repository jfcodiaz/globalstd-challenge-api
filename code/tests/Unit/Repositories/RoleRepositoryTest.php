<?php

namespace Tests\Unit\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function create(string $name)
    {
        return Role::create(['name' => $name]);
    }

    public function test_it_creates_base_roles(): void
    {
        $repo = new RoleRepository;

        $repo->createBaseRoles();

        foreach (['SuperAdmin', 'Admin', 'Employee', 'Client'] as $role) {
            $this->assertDatabaseHas('roles', ['name' => $role]);
        }
    }

    public function test_it_assigns_a_role_to_user(): void
    {
        $user = User::factory()->create();
        $repo = new RoleRepository;

        $role = $repo->create(['name' => 'Admin2']);
        $repo->assignToUser($role, $user);

        $this->assertTrue($user->roles->contains('name', 'Admin2'));
    }
}
