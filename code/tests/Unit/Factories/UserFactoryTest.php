<?php

namespace Tests\Unit\Factories;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_assigns_client_role(): void
    {
        $user = User::factory()->withRoles(['Client'])->create();

        $this->assertTrue($user->roles->contains('name', 'Client'));
    }

    public function test_user_factory_assigns_multiple_roles(): void
    {
        $user = User::factory()->withRoles(['Admin', 'Client'])->create();

        $this->assertTrue($user->roles->contains('name', 'Admin'));
        $this->assertTrue($user->roles->contains('name', 'Client'));
    }
}
