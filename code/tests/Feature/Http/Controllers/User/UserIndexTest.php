<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    const JOHNS_EMAIL = 'john@example.com';

    public function test_superadmin_can_list_users_paginated(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRoles(['SuperAdmin']);
        $this->actingAs($superAdmin);

        User::factory()->count(25)->create();

        $response = $this->getJson(route('user.index', ['per_page' => 10]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data', 'links',
        ]);

        $this->assertCount(10, $response->json('data'));
    }

    public function test_can_filter_users_by_name_email_or_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRoles(['Admin']);
        $this->actingAs($admin);

        $john = User::factory()->create([
            'name' => 'John Doe',
            'email' => self::JOHNS_EMAIL,
        ]);
        $john->assignRoles(['Client']);

        $jane = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@demo.com',
        ]);
        $jane->assignRoles(['Employee']);

        // Buscar por nombre
        $response = $this->getJson(route('user.index', ['search' => 'John']));
        $response->assertOk();
        $this->assertEquals(self::JOHNS_EMAIL, $response->json('data.0.email'));

        // Buscar por email
        $response = $this->getJson(route('user.index', ['search' => 'jane@']));
        $response->assertOk();
        $this->assertEquals('jane@demo.com', $response->json('data.0.email'));

        // Buscar por nombre de rol
        $response = $this->getJson(route('user.index', ['search' => 'Client']));
        $response->assertOk();
        $this->assertEquals(self::JOHNS_EMAIL, $response->json('data.0.email'));
    }

    public function test_client_cannot_access_user_list(): void
    {
        $client = User::factory()->create();
        $client->assignRoles(['Client']);
        $this->actingAs($client);

        $response = $this->getJson(route('user.index'));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_user_list(): void
    {
        $response = $this->getJson(route('user.index'));
        $response->assertUnauthorized();
    }

    public function test_employee_can_access_user_list(): void
    {
        $employee = User::factory()->create();
        $employee->assignRoles(['Employee']);
        $this->actingAs($employee);

        User::factory()->count(5)->create();

        $response = $this->getJson(route('user.index'));
        $response->assertOk();
        $response->assertJsonStructure(['data', 'links']);
    }
}
