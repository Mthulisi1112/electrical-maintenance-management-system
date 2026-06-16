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
        // =========================
        // FETCH ROLES SAFELY
        // =========================
        $roles = Role::all()->keyBy('slug');

        $adminRole = $roles['admin'] ?? null;
        $supervisorRole = $roles['maintenance-supervisor'] ?? null;
        $technicianRole = $roles['technician'] ?? null;
        $auditorRole = $roles['auditor'] ?? null;

        if (!$adminRole || !$supervisorRole || !$technicianRole || !$auditorRole) {
            throw new \Exception("One or more roles are missing. Run RoleSeeder first.");
        }

        // =========================
        // SIMPLE PHONE GENERATOR
        // =========================
        $phone = fn () => '07' . rand(100000000, 999999999);

        // =========================
        // ADMIN USERS
        // =========================
        $this->createUsers([
            ['email' => 'admin@emms.com', 'name' => 'Admin User', 'emp' => 'EMP-ADMIN-001'],
            ['email' => 'test.admin@emms.com', 'name' => 'Test Admin', 'emp' => 'EMP-ADMIN-002'],
        ], $adminRole->id, $phone, 'Administration');

        // =========================
        // SUPERVISORS
        // =========================
        $this->createUsers([
            ['email' => 'supervisor@emms.com', 'name' => 'Maintenance Supervisor', 'emp' => 'EMP-SUP-001'],
            ['email' => 'test.supervisor@emms.com', 'name' => 'Test Supervisor', 'emp' => 'EMP-SUP-002'],
        ], $supervisorRole->id, $phone, 'Maintenance');

        // =========================
        // TECHNICIANS
        // =========================
        $this->createUsers([
            ['email' => 'technician@emms.com', 'name' => 'Technician User', 'emp' => 'EMP-TECH-001'],
            ['email' => 'test.technician@emms.com', 'name' => 'Test Technician', 'emp' => 'EMP-TECH-002'],
        ], $technicianRole->id, $phone, 'Maintenance');

        // =========================
        // AUDITORS
        // =========================
        $this->createUsers([
            ['email' => 'auditor@emms.com', 'name' => 'Auditor User', 'emp' => 'EMP-AUD-001'],
            ['email' => 'test.auditor@emms.com', 'name' => 'Test Auditor', 'emp' => 'EMP-AUD-002'],
        ], $auditorRole->id, $phone, 'Audit');

        // =========================
        // RANDOM USERS
        // =========================
        if (User::count() < 30) {
            User::factory()
                ->count(30 - User::count())
                ->create()
                ->each(function ($user) use ($roles) {
                    $rolePool = array_filter([
                        $roles['supervisor'] ?? null,
                        $roles['technician'] ?? null,
                        $roles['auditor'] ?? null,
                    ]);

                    if ($rolePool) {
                        $user->role_id = $rolePool[array_rand($rolePool)]->id;
                        $user->save();
                    }
                });
        }
    }

    /**
     * Reusable user creator (clean + DRY)
     */
    private function createUsers(array $users, int $roleId, callable $phone, string $department): void
    {
        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'employee_id' => $u['emp'],
                    'department' => $department,
                    'phone' => $phone(),
                    'is_active' => true,
                    'role_id' => $roleId,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}