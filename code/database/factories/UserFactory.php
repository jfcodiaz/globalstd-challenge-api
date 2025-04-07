<?php

namespace Database\Factories;

use App\Models\User;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    protected static ?RoleRepository $roleRepo = null;

    public function definition(): array
    {
        if (! static::$roleRepo) {
            static::$roleRepo = app(RoleRepository::class);
        }

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'is_active' => true,
            'password' => static::$password ??= Hash::make(config('app.default_password')),
            'remember_token' => Str::random(10),
        ];
    }

    public function withRoles(array $roles): static
    {
        return $this->afterCreating(function (User $user) use ($roles) {
            foreach ($roles as $role) {
                static::$roleRepo->assignToUser($role, $user);
            }
        });
    }

    public function unverified(): static
    {
        return $this->state(fn () => [
            'email_verified_at' => null,
        ]);
    }
}
