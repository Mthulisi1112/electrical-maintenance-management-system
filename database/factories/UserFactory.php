<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
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
            'employee_id' => fake()->unique()->numerify('EMP-####'),
            'department' => fake()->randomElement(['Electrical', 'Maintenance', 'Operations', 'Engineering', 'Facilities']),
            'phone' => fake()->phoneNumber(),
            'avatar' => null,
            'is_active' => true,
            'remember_token' => Str::random(10),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => 'admin@emms.com',
            'name' => 'Admin User',
        ])->afterCreating(function (User $user) {
            $role = Role::where('slug', 'admin')->first();
            if ($role) {
                $user->role_id = $role->id;
                $user->save();
            }
        });
    }

    public function supervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => 'supervisor@emms.com',
            'name' => 'Maintenance Supervisor',
        ])->afterCreating(function (User $user) {
            $role = Role::where('slug', 'maintenance-supervisor')->first();
            if ($role) {
                $user->role_id = $role->id;
                $user->save();
            }
        });
    }

    public function technician(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => 'technician@emms.com',
            'name' => 'Technician User',
        ])->afterCreating(function (User $user) {
            $role = Role::where('slug', 'technician')->first();
            if ($role) {
                $user->role_id = $role->id;
                $user->save();
            }
        });
    }

    public function auditor(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => 'auditor@emms.com',
            'name' => 'Auditor User',
        ])->afterCreating(function (User $user) {
            $role = Role::where('slug', 'auditor')->first();
            if ($role) {
                $user->role_id = $role->id;
                $user->save();
            }
        });
    }

    public function withRole(string $roleSlug): static
    {
        return $this->afterCreating(function (User $user) use ($roleSlug) {
            $role = Role::where('slug', $roleSlug)->first();
            if ($role) {
                $user->role_id = $role->id;
                $user->save();
            }
        });
    }
}