<?php

namespace Tests\Unit\Repositories;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_user_with_default_client_role(): void
    {
        $repo = new UserRepository(new RoleRepository);

        $user = $repo->create([
            'name' => 'Juan',
            'email' => 'juan@example.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
        $this->assertTrue($user->roles->contains('name', 'Client'));
    }

    public function test_it_creates_user_with_multiple_roles(): void
    {
        $repo = new UserRepository(new RoleRepository);

        $user = $repo->create([
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'password',
        ], ['Admin', 'Employee']);

        $this->assertTrue($user->roles->contains('name', 'Admin'));
        $this->assertTrue($user->roles->contains('name', 'Employee'));
        $this->assertTrue($user->roles->contains('name', 'Client'));
    }
}
