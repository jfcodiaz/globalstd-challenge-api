<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@local.dev'],
            [
                'name' => 'Admin Test',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'collaborator@local.dev'],
            [
                'name' => 'Colaborador Demo',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@local.dev'],
            [
                'name' => 'User Demo',
                'email_verified_at' => now(),
            ]
        );

        User::factory(50)->create();
    }
}
