<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_has_users_relationship()
    {
        $role = app(RoleRepository::class)->getRandom();
        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {
            $user->roles()->attach($role);
        }
        $relatedUsers = $role->users;
        $this->assertCount(3, $relatedUsers);
        $this->assertInstanceOf(User::class, $relatedUsers->first());
        $this->assertTrue($relatedUsers->contains($users->first()));
    }
}
