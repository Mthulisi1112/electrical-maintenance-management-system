<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create roles
        $adminRole = Role::where('slug', 'admin')->first();
        $supervisorRole = Role::where('slug', 'maintenance-supervisor')->first();
        $technicianRole = Role::where('slug', 'technician')->first();
        $auditorRole = Role::where('slug', 'auditor')->first();

        // Create or update admin users
        $adminEmails = ['admin@emms.com', 'test.admin@emms.com'];
        foreach ($adminEmails as $index => $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $index === 0 ? 'Admin User' : 'Test Admin',
                    'password' => Hash::make('password'),
                    'employee_id' => 'EMP-ADMIN-00' . ($index + 1),
                    'department' => 'Administration',
                    'phone' => fake()->phoneNumber(),
                    'is_active' => true,
                    'role_id' => $adminRole?->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Create or update supervisor users
        $supervisorEmails = ['supervisor@emms.com', 'test.supervisor@emms.com'];
        foreach ($supervisorEmails as $index => $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $index === 0 ? 'Maintenance Supervisor' : 'Test Supervisor',
                    'password' => Hash::make('password'),
                    'employee_id' => 'EMP-SUP-00' . ($index + 1),
                    'department' => 'Maintenance',
                    'phone' => fake()->phoneNumber(),
                    'is_active' => true,
                    'role_id' => $supervisorRole?->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Create or update technician users
        $technicianEmails = ['technician@emms.com', 'test.technician@emms.com'];
        foreach ($technicianEmails as $index => $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $index === 0 ? 'Technician User' : 'Test Technician',
                    'password' => Hash::make('password'),
                    'employee_id' => 'EMP-TECH-00' . ($index + 1),
                    'department' => 'Maintenance',
                    'phone' => fake()->phoneNumber(),
                    'is_active' => true,
                    'role_id' => $technicianRole?->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Create or update auditor users
        $auditorEmails = ['auditor@emms.com', 'test.auditor@emms.com'];
        foreach ($auditorEmails as $index => $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $index === 0 ? 'Auditor User' : 'Test Auditor',
                    'password' => Hash::make('password'),
                    'employee_id' => 'EMP-AUD-00' . ($index + 1),
                    'department' => 'Audit',
                    'phone' => fake()->phoneNumber(),
                    'is_active' => true,
                    'role_id' => $auditorRole?->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Create additional random users only if we don't have many
        $existingUserCount = User::count();
        if ($existingUserCount < 30) {
            User::factory()
                ->count(30 - $existingUserCount)
                ->create()
                ->each(function ($user) use ($adminRole, $supervisorRole, $technicianRole, $auditorRole) {
                    // Assign random role except admin for safety
                    $roles = [$supervisorRole?->id, $technicianRole?->id, $auditorRole?->id];
                    $user->role_id = $roles[array_rand($roles)];
                    $user->save();
                });
        }
    }
}