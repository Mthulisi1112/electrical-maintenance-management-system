<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        // NOT random — roles must always be consistent
        return [
            'name' => 'Technician',
            'slug' => 'technician',
            'permissions' => [
                'view-assigned-work-orders',
                'update-work-order-status',
                'log-maintenance',
                'report-faults',
                'view-assets'
            ],
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'name' => 'Administrator',
            'slug' => 'admin',
            'permissions' => [
                'view-all',
                'create-all',
                'edit-all',
                'delete-all',
                'manage-users',
                'manage-roles',
                'view-reports',
                'export-data'
            ],
        ]);
    }

    public function supervisor(): static
    {
        return $this->state(fn () => [
            'name' => 'Maintenance Supervisor',
            'slug' => 'maintenance-supervisor',
            'permissions' => [
                'view-all',
                'create-work-orders',
                'edit-work-orders',
                'assign-work-orders',
                'verify-work-orders',
                'view-reports',
                'create-schedules'
            ],
        ]);
    }

    public function technician(): static
    {
        return $this->state(fn () => [
            'name' => 'Technician',
            'slug' => 'technician',
            'permissions' => [
                'view-assigned-work-orders',
                'update-work-order-status',
                'log-maintenance',
                'report-faults',
                'view-assets'
            ],
        ]);
    }

    public function auditor(): static
    {
        return $this->state(fn () => [
            'name' => 'Auditor',
            'slug' => 'auditor',
            'permissions' => [
                'view-all',
                'view-reports',
                'export-data'
            ],
        ]);
    }
}