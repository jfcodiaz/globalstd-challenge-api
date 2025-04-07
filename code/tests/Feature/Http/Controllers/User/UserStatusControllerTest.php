<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_activate_and_deactivate_user()
    {
        $admin = User::factory()->withRoles(['SuperAdmin'])->create();
        $target = User::factory()->create(['is_active' => false]);

        $this->actingAs($admin)
            ->patchJson("/api/users/{$target->id}/status", ['status' => 'active'])
            ->assertOk()
            ->assertJson(['message' => 'User status updated to active.']);

        $this->assertTrue($target->fresh()->is_active);

        $this->actingAs($admin)
            ->patchJson("/api/users/{$target->id}/status", ['status' => 'inactive'])
            ->assertOk()
            ->assertJson(['message' => 'User status updated to inactive.']);

        $this->assertFalse($target->fresh()->is_active);
    }

    public function test_invalid_status_returns_validation_error()
    {
        $admin = User::factory()->withRoles(['SuperAdmin'])->create();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->patchJson("/api/users/{$target->id}/status", ['status' => 'foo'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_unauthorized_roles_cannot_update_status()
    {
        $user = User::factory()->withRoles(['Client'])->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->patchJson("/api/users/{$target->id}/status", ['status' => 'active'])
            ->assertForbidden();
    }

    public function test_guest_cannot_access_status_change()
    {
        $target = User::factory()->create();

        $this->patchJson("/api/users/{$target->id}/status", ['status' => 'active'])
            ->assertUnauthorized();
    }
}
