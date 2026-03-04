<?php

namespace Database\Seeders;

use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;
use App\Models\MaintenanceSchedule;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        $technicians = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->get();

        $supervisors = User::whereHas('role', function($q) {
            $q->whereIn('slug', ['admin', 'maintenance-supervisor']);
        })->get();

        if ($technicians->isEmpty()) {
            $technicians = User::factory()->technician()->count(10)->create();
        }

        if ($supervisors->isEmpty()) {
            $supervisors = User::factory()->supervisor()->count(3)->create();
        }

        $assets = Asset::all();
        $schedules = MaintenanceSchedule::all();

        // Create pending work orders
        WorkOrder::factory()
            ->pending()
            ->count(30)
            ->create([
                'supervisor_id' => $supervisors->random()->id,
            ])
            ->each(function ($workOrder) use ($technicians, $assets, $schedules) {
                $workOrder->technician_id = $technicians->random()->id;
                $workOrder->asset_id = $assets->random()->id;
                $workOrder->maintenance_schedule_id = $schedules->isNotEmpty() ? $schedules->random()->id : null;
                $workOrder->save();
            });

        // Create in-progress work orders
        WorkOrder::factory()
            ->inProgress()
            ->count(20)
            ->create([
                'supervisor_id' => $supervisors->random()->id,
            ])
            ->each(function ($workOrder) use ($technicians, $assets) {
                $workOrder->technician_id = $technicians->random()->id;
                $workOrder->asset_id = $assets->random()->id;
                $workOrder->save();
            });

        // Create completed work orders
        WorkOrder::factory()
            ->completed()
            ->count(40)
            ->create([
                'supervisor_id' => $supervisors->random()->id,
            ])
            ->each(function ($workOrder) use ($technicians, $assets) {
                $workOrder->technician_id = $technicians->random()->id;
                $workOrder->asset_id = $assets->random()->id;
                $workOrder->save();
            });

        // Create verified work orders
        WorkOrder::factory()
            ->verified()
            ->count(50)
            ->create([
                'supervisor_id' => $supervisors->random()->id,
            ])
            ->each(function ($workOrder) use ($technicians, $assets) {
                $workOrder->technician_id = $technicians->random()->id;
                $workOrder->asset_id = $assets->random()->id;
                $workOrder->save();
            });

        // Create emergency work orders
        WorkOrder::factory()
            ->emergency()
            ->count(15)
            ->create([
                'supervisor_id' => $supervisors->random()->id,
            ])
            ->each(function ($workOrder) use ($technicians, $assets) {
                $workOrder->technician_id = $technicians->random()->id;
                $workOrder->asset_id = $assets->random()->id;
                $workOrder->save();
            });

        // Create work orders for specific assets
        $criticalAssets = Asset::where('status', 'operational')->take(20)->get();
        foreach ($criticalAssets as $asset) {
            WorkOrder::factory()
                ->preventive()
                ->count(3)
                ->create([
                    'asset_id' => $asset->id,
                    'supervisor_id' => $supervisors->random()->id,
                    'technician_id' => $technicians->random()->id,
                ]);
        }

        // Create historical work orders (older dates)
        WorkOrder::factory()
            ->verified()
            ->count(100)
            ->create([
                'supervisor_id' => $supervisors->random()->id,
                'created_at' => fake()->dateTimeBetween('-6 months', '-1 month'),
                'scheduled_date' => fake()->dateTimeBetween('-6 months', '-1 month'),
                'completed_date' => fake()->dateTimeBetween('-5 months', '-1 month'),
                'verified_at' => fake()->dateTimeBetween('-5 months', '-1 month'),
            ])
            ->each(function ($workOrder) use ($technicians, $assets) {
                $workOrder->technician_id = $technicians->random()->id;
                $workOrder->asset_id = $assets->random()->id;
                $workOrder->save();
            });
    }
}