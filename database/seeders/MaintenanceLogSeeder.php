<?php

namespace Database\Seeders;

use App\Models\MaintenanceLog;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaintenanceLogSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $completedWorkOrders = WorkOrder::whereIn('status', ['completed', 'verified'])->get();

        if ($completedWorkOrders->isEmpty()) {
            $this->command->info('No completed work orders found. Skipping maintenance logs.');
            return;
        }

        // Create one log per completed work order
        foreach ($completedWorkOrders as $workOrder) {
            if (!MaintenanceLog::where('work_order_id', $workOrder->id)->exists()) {
                MaintenanceLog::factory()
                    ->forWorkOrder($workOrder)
                    ->create();
            }
        }

        // Additional successful logs
        $randomWorkOrders = $completedWorkOrders->random(min(50, $completedWorkOrders->count()));

        foreach ($randomWorkOrders as $workOrder) {
            MaintenanceLog::factory()
                ->successful()
                ->forWorkOrder($workOrder)
                ->create();
        }

        // Failed logs
        $failedCount = min(15, $completedWorkOrders->count());
        $failedWorkOrders = $completedWorkOrders->random($failedCount);

        foreach ($failedWorkOrders as $workOrder) {
            MaintenanceLog::factory()
                ->failed()
                ->forWorkOrder($workOrder)
                ->create();
        }

        // Logs per asset
        $assets = Asset::inRandomOrder()->take(30)->get();

        if ($assets->isEmpty()) {
            $assets = Asset::factory()->count(10)->create();
        }

        foreach ($assets as $asset) {
            $count = $faker->numberBetween(1, 5);

            for ($i = 0; $i < $count; $i++) {
                MaintenanceLog::factory()
                    ->forAsset($asset)
                    ->create([
                        'performed_by' => $this->getTechnicianId($faker),
                        'work_order_id' => WorkOrder::inRandomOrder()->first()?->id,
                    ]);
            }
        }

        // Logs per technician
        $technicians = User::whereHas('role', function ($q) {
            $q->where('slug', 'technician');
        })->get();

        if ($technicians->isEmpty()) {
            $technicians = User::factory()->technician()->count(5)->create();
        }

        foreach ($technicians as $technician) {
            $count = $faker->numberBetween(5, 15);

            for ($i = 0; $i < $count; $i++) {
                MaintenanceLog::factory()
                    ->performedBy($technician)
                    ->create([
                        'work_order_id' => WorkOrder::inRandomOrder()->first()?->id,
                        'asset_id' => Asset::inRandomOrder()->first()?->id
                            ?? Asset::factory()->create()->id,
                    ]);
            }
        }

        // Historical logs
        $historicalCount = min(150, $completedWorkOrders->count() * 2);

        for ($i = 0; $i < $historicalCount; $i++) {
            $workOrder = $completedWorkOrders->random();

            MaintenanceLog::factory()
                ->forWorkOrder($workOrder)
                ->create([
                    'created_at' => $faker->dateTimeBetween('-1 year', '-1 month'),
                ]);
        }

        $this->command->info('Maintenance logs seeded successfully!');
    }

    private function getTechnicianId($faker)
    {
        return User::whereHas('role', fn ($q) =>
            $q->where('slug', 'technician')
        )->inRandomOrder()->first()?->id
        ?? User::factory()->technician()->create()->id;
    }
}