<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $roles = [
            'admin' => [
                'name' => 'Administrator',
                'permissions' => ['view-all', 'create-all', 'edit-all', 'delete-all', 'manage-users', 'manage-roles', 'view-reports', 'export-data']
            ],
            'maintenance-supervisor' => [
                'name' => 'Maintenance Supervisor',
                'permissions' => ['view-all', 'create-work-orders', 'edit-work-orders', 'assign-work-orders', 'verify-work-orders', 'view-reports', 'create-schedules']
            ],
            'technician' => [
                'name' => 'Technician',
                'permissions' => ['view-assigned-work-orders', 'update-work-order-status', 'log-maintenance', 'report-faults', 'view-assets']
            ],
            'auditor' => [
                'name' => 'Auditor',
                'permissions' => ['view-all', 'view-reports', 'export-data']
            ],
        ];

        $role = fake()->randomElement(array_keys($roles));
        
        return [
            'name' => $roles[$role]['name'],
            'slug' => $role,
            'permissions' => json_encode($roles[$role]['permissions']),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Administrator',
            'slug' => 'admin',
            'permissions' => json_encode(['view-all', 'create-all', 'edit-all', 'delete-all', 'manage-users', 'manage-roles', 'view-reports', 'export-data']),
        ]);
    }

    public function supervisor(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Maintenance Supervisor',
            'slug' => 'maintenance-supervisor',
            'permissions' => json_encode(['view-all', 'create-work-orders', 'edit-work-orders', 'assign-work-orders', 'verify-work-orders', 'view-reports', 'create-schedules']),
        ]);
    }

    public function technician(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Technician',
            'slug' => 'technician',
            'permissions' => json_encode(['view-assigned-work-orders', 'update-work-order-status', 'log-maintenance', 'report-faults', 'view-assets']),
        ]);
    }

    public function auditor(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Auditor',
            'slug' => 'auditor',
            'permissions' => json_encode(['view-all', 'view-reports', 'export-data']),
        ]);
    }
}