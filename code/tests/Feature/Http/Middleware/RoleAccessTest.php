<?php

namespace Tests\Feature\Http\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected array $routes = [
        'SuperAdmin' => '/api/test/superadmin',
        'Admin' => '/api/test/admin',
        'Employee' => '/api/test/employee',
        'Client' => '/api/test/client',
    ];

    public function test_user_with_correct_role_can_access(): void
    {
        foreach ($this->routes as $role => $route) {
            $user = User::factory()->withRoles([$role])->create();

            $this->actingAs($user)->getJson($route)
                ->assertOk()
                ->assertJson([
                    'name' => $user->name,
                    'roles' => [$role],
                ]);
        }
    }

    public function test_user_with_wrong_role_cannot_access_other_routes(): void
    {
        $user = User::factory()->withRoles(['Client'])->create();

        foreach ($this->routes as $role => $route) {
            if ($role === 'Client') {
                $this->actingAs($user)->getJson($route)->assertOk();
            } else {
                $this->actingAs($user)->getJson($route)->assertForbidden();
            }
        }
    }

    public function test_guest_user_cannot_access_any_route(): void
    {
        foreach ($this->routes as $route) {
            $this->getJson($route)->assertUnauthorized();
        }
    }
}
