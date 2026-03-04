<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            AssetSeeder::class,
            MaintenanceScheduleSeeder::class,
            WorkOrderSeeder::class,
            FaultSeeder::class,
            MaintenanceLogSeeder::class,
        ]);
    }
}