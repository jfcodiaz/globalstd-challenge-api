<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected string $defaultPassword;

    const TEST_EMAIL = 'admin@test.com';

    protected function setUp(): void
    {
        parent::setUp();
        $this->defaultPassword = config('app.default_password');
    }

    public function test_registered_user_can_get_token(): void
    {
        User::factory()->create([
            'email' => self::TEST_EMAIL,
            'password' => Hash::make($this->defaultPassword),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => self::TEST_EMAIL,
            'password' => $this->defaultPassword,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => self::TEST_EMAIL,
            'password' => Hash::make($this->defaultPassword),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => self::TEST_EMAIL,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_with_missing_fields(): void
    {
        $response = $this->postJson(route('login'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_fails_with_invalid_email_format(): void
    {
        $response = $this->postJson(route('login'), [
            'email' => 'not-an-email',
            'password' => 'something',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
