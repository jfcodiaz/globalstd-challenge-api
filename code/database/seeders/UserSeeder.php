<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->withRoles(['Client'])->create([
            'email' => 'user@local.dev',
            'name' => 'User Demo',
        ]);

        User::factory()->withRoles(['Admin'])->create([
            'email' => 'collaborator@local.dev',
            'name' => 'Colaborador Demo',
        ]);

        User::factory()->withRoles(['SuperAdmin'])->create([
            'email' => 'admin@local.dev',
            'name' => 'Admin Test',
        ]);
        User::factory(150)->withRoles(['Client'])->create();
        User::factory(10)->withRoles(['Employee'])->create([

        ]);
        User::factory(10)->withRoles(['Employee'])->create([
            'is_active' => false,
        ]);
        User::factory(10)->withRoles(['Client'])->create([
            'is_active' => false,
        ]);
    }
}
