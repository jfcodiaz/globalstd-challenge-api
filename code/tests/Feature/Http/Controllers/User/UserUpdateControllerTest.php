<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_update_any_user_and_roles(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRoles(['SuperAdmin']);
        $this->actingAs($superAdmin);

        $user = User::factory()->create();
        $user->assignRoles(['Client']);

        $response = $this->postJson(route('user.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => 'newpassword',
            'roles' => ['Employee'],
        ]);

        $response->assertOk();
        $this->assertEquals('Updated Name', $user->fresh()->name);
        $this->assertTrue($user->hasAnyRole('Employee'));
        $this->assertFalse($user->hasAnyRole('Client'));
    }

    public function test_admin_can_update_any_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRoles('Admin');
        $this->actingAs($admin);

        $user = User::factory()->create();

        $response = $this->postJson(route('user.update', $user), [
            'name' => 'Admin Edited',
            'email' => 'admin-edited@example.com',
        ]);

        $response->assertOk();
        $this->assertEquals('Admin Edited', $user->fresh()->name);
    }

    public function test_normal_user_can_update_their_own_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Original',
            'email' => 'original@example.com',
        ]);
        $user->assignRoles(['Client']);
        $this->actingAs($user);

        $response = $this->postJson(route('user.update', $user), [
            'name' => 'Self Updated',
            'email' => 'self@example.com',
            'password' => 'newsecurepass',
        ]);

        $response->assertOk();
        $this->assertEquals('Self Updated', $user->fresh()->name);
    }

    public function test_normal_user_cannot_update_other_users(): void
    {
        $user = User::factory()->create();
        $user->assignRoles(['Client']);

        $target = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('user.update', $target), [
            'name' => 'Hacker',
        ]);

        $response->assertForbidden();
    }

    public function test_guest_cannot_update_users(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('user.update', $user), [
            'name' => 'Guest Hack',
        ]);

        $response->assertUnauthorized();
    }

    public function test_non_admin_user_cannot_update_roles_even_own(): void
    {
        $user = User::factory()->create();
        $user->assignRoles(['Employee']);
        $this->actingAs($user);

        $response = $this->postJson(route('user.update', $user), [
            'roles' => ['SuperAdmin'],
        ]);

        $response->assertOk();
        $this->assertFalse($user->fresh()->hasAnyRole('SuperAdmin'));
        $this->assertTrue($user->hasAnyRole('Employee'));
    }

    public function test_superadmin_can_replace_existing_roles(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRoles(['SuperAdmin']);
        $this->actingAs($superAdmin);

        $user = User::factory()->create();
        $user->assignRoles(['Client', 'Employee']);

        $response = $this->postJson(route('user.update', $user), [
            'roles' => ['Admin'],
        ]);

        $response->assertOk();

        $user->refresh();
        $this->assertTrue($user->hasAnyRole('Admin'));
        $this->assertFalse($user->hasAnyRole('Client'));
        $this->assertFalse($user->hasAnyRole('Employee'));
    }

    public function test_admin_can_replace_existing_roles(): void
    {
        $admin = User::factory()->create();
        $admin->assignRoles(['Admin']);
        $this->actingAs($admin);

        $user = User::factory()->create();
        $user->assignRoles(['Client', 'Employee']);

        $response = $this->postJson(route('user.update', $user), [
            'roles' => ['Employee'],
        ]);

        $response->assertOk();

        $user->refresh();
        $this->assertTrue($user->hasAnyRole('Employee'));
        $this->assertFalse($user->hasAnyRole('Client'));
        $this->assertFalse($user->hasAnyRole('Admin'));
    }
}
