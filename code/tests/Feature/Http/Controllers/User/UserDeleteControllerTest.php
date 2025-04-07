<?php

namespace Tests\Feature\Http\Controllers\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeleteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_soft_delete_user()
    {
        $admin = User::factory()->withRoles(['SuperAdmin'])->create();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->deleteJson("/api/users/{$target->id}")
            ->assertOk()
            ->assertJson(['message' => 'User successfully deleted (soft).']);

        $this->assertSoftDeleted($target);
    }

    public function test_unauthorized_roles_cannot_delete_user()
    {
        $user = User::factory()->withRoles(['Client'])->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson("/api/users/{$target->id}")
            ->assertForbidden();
    }

    public function test_guest_cannot_delete_user()
    {
        $target = User::factory()->create();

        $this->deleteJson("/api/users/{$target->id}")->assertUnauthorized();
    }
}
