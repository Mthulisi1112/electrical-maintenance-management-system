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
        $technicians = User::whereHas('role', fn($q) =>
            $q->where('slug', 'technician')
        )->get();

        $supervisors = User::whereHas('role', fn($q) =>
            $q->whereIn('slug', ['admin', 'maintenance-supervisor'])
        )->get();

        if ($technicians->isEmpty()) {
            $technicians = User::factory()->technician()->count(10)->create();
        }

        if ($supervisors->isEmpty()) {
            $supervisors = User::factory()->supervisor()->count(3)->create();
        }

        $assets = Asset::all();
        if ($assets->isEmpty()) {
            $assets = Asset::factory()->count(20)->create();
        }

        $schedules = MaintenanceSchedule::all();

        $random = fn($collection) => $collection->random()->id;

        $make = function (array $overrides = []) use (
            $technicians,
            $supervisors,
            $assets,
            $schedules,
            $random
        ) {
            return array_merge([
                'asset_id' => $random($assets),
                'technician_id' => $random($technicians),
                'supervisor_id' => $random($supervisors),
                'maintenance_schedule_id' => $schedules->isNotEmpty()
                    ? $random($schedules)
                    : null,
            ], $overrides);
        };

        // Pending
        WorkOrder::factory()->count(30)->pending()->create($make());

        // In Progress
        WorkOrder::factory()->count(20)->inProgress()->create($make());

        // Completed
        WorkOrder::factory()->count(40)->completed()->create($make());

        // Verified
        WorkOrder::factory()->count(50)->verified()->create($make());

        // Emergency
        WorkOrder::factory()->count(15)->emergency()->create($make());

        // Preventive per asset
        $criticalAssets = Asset::where('status', 'operational')->take(20)->get();
        foreach ($criticalAssets as $asset) {
            WorkOrder::factory()
                ->count(3)
                ->preventive()
                ->create($make([
                    'asset_id' => $asset->id,
                ]));
        }

        // Historical verified
        WorkOrder::factory()
            ->count(100)
            ->verified()
            ->create($make([
                'created_at' => now()->subMonths(rand(2, 6)),
                'scheduled_date' => now()->subMonths(rand(2, 6)),
                'completed_date' => now()->subMonths(rand(1, 5)),
                'verified_at' => now()->subMonths(rand(1, 5)),
            ]));
    }
}