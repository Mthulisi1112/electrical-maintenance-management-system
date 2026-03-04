<?php

namespace Database\Seeders;

use App\Models\MaintenanceSchedule;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaintenanceScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $supervisors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['admin', 'maintenance-supervisor']);
        })->get();

        if ($supervisors->isEmpty()) {
            $supervisors = User::factory()->supervisor()->count(3)->create();
        }

        $assets = Asset::all();

        // Create schedules for each asset
        foreach ($assets as $asset) {
            // Create monthly schedule for most assets
            MaintenanceSchedule::factory()
                ->monthly()
                ->create([
                    'asset_id' => $asset->id,
                    'created_by' => $supervisors->random()->id,
                    'title' => 'Monthly Preventive Maintenance - ' . $asset->name,
                ]);

            // Create quarterly schedule for critical assets
            if (in_array($asset->type, ['transformer', 'mcc', 'switchgear'])) {
                MaintenanceSchedule::factory()
                    ->create([
                        'asset_id' => $asset->id,
                        'frequency' => 'quarterly',
                        'priority' => 'high',
                        'created_by' => $supervisors->random()->id,
                        'title' => 'Quarterly Inspection - ' . $asset->name,
                    ]);
            }

            // Create annual schedule for all assets
            MaintenanceSchedule::factory()
                ->create([
                    'asset_id' => $asset->id,
                    'frequency' => 'annual',
                    'created_by' => $supervisors->random()->id,
                    'title' => 'Annual Overhaul - ' . $asset->name,
                    'estimated_duration_minutes' => 480,
                ]);
        }

        // Create some high priority schedules
        MaintenanceSchedule::factory()
            ->highPriority()
            ->count(20)
            ->create([
                'created_by' => $supervisors->random()->id
            ]);

        // Create some critical schedules
        MaintenanceSchedule::factory()
            ->critical()
            ->count(10)
            ->create([
                'created_by' => $supervisors->random()->id
            ]);

        // Create some inactive schedules
        MaintenanceSchedule::factory()
            ->inactive()
            ->count(15)
            ->create([
                'created_by' => $supervisors->random()->id
            ]);

        // Create daily schedules for critical equipment
        $criticalAssets = Asset::whereIn('type', ['transformer', 'vfd'])->take(10)->get();
        foreach ($criticalAssets as $asset) {
            MaintenanceSchedule::factory()
                ->daily()
                ->create([
                    'asset_id' => $asset->id,
                    'created_by' => $supervisors->random()->id,
                    'title' => 'Daily Check - ' . $asset->name,
                    'estimated_duration_minutes' => 30,
                ]);
        }

        // Create weekly schedules
        $weeklyAssets = Asset::where('status', 'operational')->take(30)->get();
        foreach ($weeklyAssets as $asset) {
            MaintenanceSchedule::factory()
                ->weekly()
                ->create([
                    'asset_id' => $asset->id,
                    'created_by' => $supervisors->random()->id,
                    'title' => 'Weekly Maintenance - ' . $asset->name,
                    'estimated_duration_minutes' => 120,
                ]);
        }
    }
}