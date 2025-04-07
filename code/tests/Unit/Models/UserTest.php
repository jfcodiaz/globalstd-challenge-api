<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_has_any_role_with_roles_loaded()
    {
        $user = User::factory()->withRoles(['Admin'])->create();
        $user->load('roles');

        $this->assertTrue($user->hasAnyRole(['Admin']));
        $this->assertFalse($user->hasAnyRole(['Client']));
    }

    public function test_has_any_role_without_roles_loaded()
    {
        $user = User::factory()->withRoles(['Admin'])->create();

        $this->assertTrue($user->hasAnyRole(['Admin']));
        $this->assertFalse($user->hasAnyRole(['Client']));
    }
}
