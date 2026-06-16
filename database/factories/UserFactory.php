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
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),

            'employee_id' => 'EMP-' . $this->faker->unique()->numberBetween(1000, 9999),

            'department' => $this->faker->randomElement([
                'Electrical',
                'Maintenance',
                'Operations',
                'Engineering',
                'Facilities',
            ]),

            'phone' => '07' . $this->faker->numberBetween(100000000, 999999999),

            'avatar' => null,
            'is_active' => true,

            'remember_token' => Str::random(10),

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'name' => 'Admin User',
            'email' => 'admin@emms.com',
        ]);
    }

    public function supervisor(): static
    {
        return $this->state(fn () => [
            'name' => 'Maintenance Supervisor',
            'email' => 'supervisor@emms.com',
        ]);
    }

    public function technician(): static
    {
        return $this->state(fn () => [
            'name' => 'Technician User',
            'email' => 'technician@emms.com',
        ]);
    }

    public function auditor(): static
    {
        return $this->state(fn () => [
            'name' => 'Auditor User',
            'email' => 'auditor@emms.com',
        ]);
    }
}