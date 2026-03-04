<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'permissions' => json_encode([
                    'view-all',
                    'create-all',
                    'edit-all',
                    'delete-all',
                    'manage-users',
                    'manage-roles',
                    'view-reports',
                    'export-data',
                    'manage-settings',
                    'view-audit-logs'
                ])
            ],
            [
                'name' => 'Maintenance Supervisor',
                'slug' => 'maintenance-supervisor',
                'permissions' => json_encode([
                    'view-all',
                    'create-work-orders',
                    'edit-work-orders',
                    'assign-work-orders',
                    'verify-work-orders',
                    'view-reports',
                    'create-schedules',
                    'edit-schedules',
                    'approve-parts-requests',
                    'view-maintenance-history'
                ])
            ],
            [
                'name' => 'Technician',
                'slug' => 'technician',
                'permissions' => json_encode([
                    'view-assigned-work-orders',
                    'update-work-order-status',
                    'log-maintenance',
                    'report-faults',
                    'view-assets',
                    'view-maintenance-schedules',
                    'request-parts',
                    'upload-images',
                    'add-technician-remarks'
                ])
            ],
            [
                'name' => 'Auditor',
                'slug' => 'auditor',
                'permissions' => json_encode([
                    'view-all',
                    'view-reports',
                    'export-data',
                    'view-audit-logs',
                    'view-maintenance-history',
                    'view-fault-history',
                    'view-compliance-records'
                ])
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
