<?php

namespace Tests\Unit\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Route;
use Tests\TestCase;

class BlockIfInactiveOrDeletedTest extends TestCase
{
    use RefreshDatabase;

    const URL_FAKE = '/middleware-check';

    protected function setUp(): void
    {
        parent::setUp();
        Route::middleware(['auth:sanctum', 'block_inactive'])
            ->get(
                self::URL_FAKE,
                fn () => response()->json(
                    ['ok' => true]
                )
            );
    }

    public function test_active_user_can_access()
    {
        $user = User::factory()->withRoles(['Client'])->create(['is_active' => true]);

        $this->actingAs($user)->getJson(self::URL_FAKE)->assertOk()->assertJson(['ok' => true]);
    }

    public function test_inactive_user_is_blocked()
    {
        $user = User::factory()->withRoles(['Client'])->create([
            'is_active' => false,
        ]);

        $this->actingAs($user)->getJson(self::URL_FAKE)
            ->assertForbidden()
            ->assertJson(['message' => 'User is inactive or has been deleted.']);
    }

    public function test_deleted_user_is_blocked()
    {
        $user = User::factory()->withRoles(['Client'])->create();
        $user->delete();

        $this->actingAs($user)->getJson(self::URL_FAKE)
            ->assertForbidden()
            ->assertJson(['message' => 'User is inactive or has been deleted.']);
    }

    public function test_guest_is_unauthorized()
    {
        $this->getJson('/middleware-check')->assertUnauthorized();
    }
}
