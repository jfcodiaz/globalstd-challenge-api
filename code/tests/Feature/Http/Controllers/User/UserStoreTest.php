<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStoreTest extends TestCase
{
    use RefreshDatabase;

    const JUANS_EMAIL = 'juan@example.com';

    public function test_superadmin_can_create_user_with_roles(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRoles(['SuperAdmin']);

        $this->actingAs($superAdmin);

        $response = $this->postJson(route('user.store'), [
            'name' => 'Juan Pérez',
            'email' => self::JUANS_EMAIL,
            'password' => 'secreto123',
            'roles' => ['Client', 'Employee'],
        ]);
        $response->assertCreated();

        $response->assertJsonStructure([
            'data' => [
                'id', 'name', 'email',
                'roles' => [['id', 'name']],
                'avatar',
            ],
        ]);

        $this->assertDatabaseHas('users', ['email' => self::JUANS_EMAIL]);
        $user = User::where('email', self::JUANS_EMAIL)->first();
        $this->assertTrue($user->hasAnyRole('Client'));
        $this->assertTrue($user->hasAnyRole('Employee'));
    }

    public function test_user_creation_requires_valid_data(): void
    {
        $admin = User::factory()->create();
        $admin->assignRoles('Admin');

        $this->actingAs($admin);

        $response = $this->postJson(route('user.store'), [
            'name' => '',
            'email' => 'invalid',
            'password' => '123',
            'roles' => ['InvalidRole'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password', 'roles.0']);
    }

    public function test_unauthorized_user_cannot_create_users(): void
    {
        $user = User::factory()->create();
        $user->assignRoles('Client');

        $this->actingAs($user);

        $response = $this->postJson(route('user.store'), [
            'name' => 'Not allowed',
            'email' => 'nope@example.com',
            'password' => '12345678',
        ]);

        $response->assertForbidden();
    }

    public function test_guest_cannot_create_users(): void
    {
        $response = $this->postJson(route('user.store'), [
            'name' => 'Guest',
            'email' => 'guest@example.com',
            'password' => '12345678',
        ]);

        $response->assertUnauthorized();
    }
}
