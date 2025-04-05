<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    protected string $url;

    protected function setUp(): void
    {
        parent::setUp();
        $this->url = route('logout');
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson($this->url)
            ->assertOk()
            ->assertJson(['message' => 'Logged out']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_logout_requires_authentication()
    {
        $response = $this->postJson($this->url);
        $response->assertUnauthorized();
    }
}
