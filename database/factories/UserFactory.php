<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    // --- ESTADOS DE ROL (Los que el Seeder está buscando) ---

    public function asBranchManager(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('branch_manager');
        });
    }

    public function asBranchStaff(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('branch_staff');
        });
    }

    public function asBusinessOwner(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('business_owner');
        });
    }

    public function asBusinessStaff(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('business_staff');
        });
    }
}