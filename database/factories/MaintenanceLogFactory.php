<?php

namespace Database\Factories;

use App\Models\MaintenanceLog;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceLogFactory extends Factory
{
    protected $model = MaintenanceLog::class;

    public function definition(): array
    {
        $faker = $this->faker;

        $maintenanceTypes = ['preventive', 'corrective', 'inspection', 'calibration', 'repair'];
        $results = ['successful', 'partial', 'failed', 'deferred'];

        $workOrder = WorkOrder::inRandomOrder()->first();

        $measurements = [
            ['name' => 'Temperature', 'value' => $faker->numberBetween(25, 85), 'unit' => '°C'],
            ['name' => 'Vibration', 'value' => $faker->randomFloat(2, 0.5, 7.5), 'unit' => 'mm/s'],
            ['name' => 'Current', 'value' => $faker->numberBetween(10, 400), 'unit' => 'A'],
            ['name' => 'Voltage', 'value' => $faker->numberBetween(380, 420), 'unit' => 'V'],
            ['name' => 'Resistance', 'value' => $faker->randomFloat(2, 0.1, 100), 'unit' => 'MΩ'],
            ['name' => 'Pressure', 'value' => $faker->numberBetween(2, 10), 'unit' => 'bar'],
        ];

        // If NO work order exists (safe fallback)
        if (!$workOrder) {
            return [
                'work_order_id' => null,
                'asset_id' => Asset::inRandomOrder()->first()?->id,
                'performed_by' => User::inRandomOrder()->first()?->id,

                'maintenance_type' => $faker->randomElement($maintenanceTypes),
                'actions_taken' => $faker->paragraph(3),

                'measurements' => json_encode($faker->randomElements($measurements, rand(2, 4))),
                'parts_used' => json_encode($this->parts($faker)),

                'time_spent_minutes' => $faker->numberBetween(30, 300),
                'observations' => $faker->paragraph(),

                'attachments' => null,
                'result' => $faker->randomElement($results),

                'next_maintenance_date' => $faker->optional(0.6)->dateTimeBetween('+1 month', '+6 months'),

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return [
            'work_order_id' => $workOrder->id,
            'asset_id' => $workOrder->asset_id,
            'performed_by' => $workOrder->technician_id,

            'maintenance_type' => $this->mapType($workOrder->type),

            'actions_taken' => $faker->paragraph(3),

            'measurements' => json_encode($faker->randomElements($measurements, rand(2, 4))),

            'parts_used' => json_encode($this->parts($faker)),

            'time_spent_minutes' => $workOrder->time_spent_minutes ?? $faker->numberBetween(30, 300),

            'observations' => $faker->paragraph(),

            'attachments' => null,

            'result' => $faker->randomElement($results),

            'next_maintenance_date' => $faker->optional(0.6)->dateTimeBetween('+1 month', '+6 months'),

            'created_at' => $workOrder->completed_date ?? now(),
            'updated_at' => now(),
        ];
    }

    private function mapType($type): string
    {
        return match ($type) {
            'preventive' => 'preventive',
            'corrective', 'emergency' => 'corrective',
            'inspection' => 'inspection',
            'calibration' => 'calibration',
            'repair' => 'repair',
            default => 'preventive',
        };
    }

    private function parts($faker): array
    {
        $parts = [
            ['name' => 'Bearing 6304', 'quantity' => 2],
            ['name' => 'Oil Seal', 'quantity' => 1],
            ['name' => 'Grease', 'quantity' => 1],
            ['name' => 'Filter', 'quantity' => 1],
            ['name' => 'Contactor', 'quantity' => 1],
            ['name' => 'Fuse 10A', 'quantity' => 2],
        ];

        return $faker->randomElements($parts, rand(1, 3));
    }

    /* States */

    public function successful(): static
    {
        return $this->state(fn () => ['result' => 'successful']);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['result' => 'failed']);
    }

    public function preventive(): static
    {
        return $this->state(fn () => ['maintenance_type' => 'preventive']);
    }

    public function corrective(): static
    {
        return $this->state(fn () => ['maintenance_type' => 'corrective']);
    }

    public function inspection(): static
    {
        return $this->state(fn () => ['maintenance_type' => 'inspection']);
    }

    public function calibration(): static
    {
        return $this->state(fn () => ['maintenance_type' => 'calibration']);
    }

    public function repair(): static
    {
        return $this->state(fn () => ['maintenance_type' => 'repair']);
    }

    public function forAsset(Asset $asset): static
    {
        return $this->state(fn () => ['asset_id' => $asset->id]);
    }

    public function performedBy(User $user): static
    {
        return $this->state(fn () => ['performed_by' => $user->id]);
    }

    public function forWorkOrder(WorkOrder $workOrder): static
    {
        return $this->state(fn () => [
            'work_order_id' => $workOrder->id,
            'asset_id' => $workOrder->asset_id,
            'performed_by' => $workOrder->technician_id,
            'maintenance_type' => $this->mapType($workOrder->type),
            'time_spent_minutes' => $workOrder->time_spent_minutes ?? 60,
            'created_at' => $workOrder->completed_date ?? now(),
            'updated_at' => now(),
        ]);
    }
}