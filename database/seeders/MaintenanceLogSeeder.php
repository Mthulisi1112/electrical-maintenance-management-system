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
        // Get completed work orders
        $completedWorkOrders = WorkOrder::whereIn('status', ['completed', 'verified'])->get();

        if ($completedWorkOrders->isEmpty()) {
            $this->command->info('No completed work orders found. Skipping maintenance logs.');
            return;
        }

        // Create logs for completed work orders
        foreach ($completedWorkOrders as $workOrder) {
            // Only create log if it doesn't already exist
            if (!MaintenanceLog::where('work_order_id', $workOrder->id)->exists()) {
                MaintenanceLog::factory()
                    ->forWorkOrder($workOrder)
                    ->create();
            }
        }

        // Create additional successful logs for random work orders
        $randomWorkOrders = WorkOrder::whereIn('status', ['completed', 'verified'])
            ->inRandomOrder()
            ->take(50)
            ->get();

        foreach ($randomWorkOrders as $workOrder) {
            MaintenanceLog::factory()
                ->successful()
                ->forWorkOrder($workOrder)
                ->create();
        }

        // Create some failed maintenance logs
        $failedLogsCount = min(15, $completedWorkOrders->count());
        $failedWorkOrders = $completedWorkOrders->random($failedLogsCount);
        
        foreach ($failedWorkOrders as $workOrder) {
            MaintenanceLog::factory()
                ->failed()
                ->forWorkOrder($workOrder)
                ->create();
        }

        // Create logs for specific assets
        $assets = Asset::inRandomOrder()->take(30)->get();
        foreach ($assets as $asset) {
            $count = fake()->numberBetween(1, 5);
            for ($i = 0; $i < $count; $i++) {
                MaintenanceLog::factory()
                    ->forAsset($asset)
                    ->create([
                        'performed_by' => User::whereHas('role', function($q) {
                            $q->where('slug', 'technician');
                        })->inRandomOrder()->first()?->id ?? User::factory()->technician()->create()->id,
                        'work_order_id' => WorkOrder::inRandomOrder()->first()?->id,
                    ]);
            }
        }

        // Create logs for specific technicians
        $technicians = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->get();

        foreach ($technicians as $technician) {
            $count = fake()->numberBetween(5, 15);
            for ($i = 0; $i < $count; $i++) {
                MaintenanceLog::factory()
                    ->performedBy($technician)
                    ->create([
                        'work_order_id' => WorkOrder::inRandomOrder()->first()?->id,
                        'asset_id' => Asset::inRandomOrder()->first()?->id ?? Asset::factory()->create()->id,
                    ]);
            }
        }

        // Create historical logs
        $historicalCount = min(150, $completedWorkOrders->count() * 2);
        for ($i = 0; $i < $historicalCount; $i++) {
            $workOrder = $completedWorkOrders->random();
            MaintenanceLog::factory()
                ->forWorkOrder($workOrder)
                ->create([
                    'created_at' => fake()->dateTimeBetween('-1 year', '-1 month'),
                ]);
        }

        $this->command->info('Maintenance logs seeded successfully!');
    }
}