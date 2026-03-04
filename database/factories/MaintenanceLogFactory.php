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
        // Valid maintenance types only'
        $maintenanceTypes = ['preventive', 'corrective', 'inspection', 'calibration', 'repair'];
        $results = ['successful', 'partial', 'failed', 'deferred'];
        
        $measurements = [
            ['name' => 'Temperature', 'value' => fake()->numberBetween(25, 85), 'unit' => '°C'],
            ['name' => 'Vibration', 'value' => fake()->randomFloat(2, 0.5, 7.5), 'unit' => 'mm/s'],
            ['name' => 'Current', 'value' => fake()->numberBetween(10, 400), 'unit' => 'A'],
            ['name' => 'Voltage', 'value' => fake()->numberBetween(380, 420), 'unit' => 'V'],
            ['name' => 'Resistance', 'value' => fake()->randomFloat(2, 0.1, 100), 'unit' => 'MΩ'],
            ['name' => 'Pressure', 'value' => fake()->numberBetween(2, 10), 'unit' => 'bar'],
        ];

        // Get a random work order or create one
        $workOrder = WorkOrder::inRandomOrder()->first();
        
        // If no work order exists, we need to handle this gracefully
        if (!$workOrder) {
            // You might want to return early or handle this differently
            // For now, we'll return a minimal set and let the seeder handle it
            return [
                'work_order_id' => null, // This will be set by the seeder
                'asset_id' => null,
                'performed_by' => null,
                'maintenance_type' => fake()->randomElement($maintenanceTypes),
                'actions_taken' => fake()->paragraphs(3, true),
                'measurements' => json_encode(fake()->randomElements($measurements, fake()->numberBetween(2, 4))),
                'parts_used' => json_encode($this->generatePartsUsed()),
                'time_spent_minutes' => fake()->numberBetween(30, 300),
                'observations' => fake()->paragraph(),
                'attachments' => null,
                'result' => fake()->randomElement($results),
                'next_maintenance_date' => fake()->optional(0.6)->dateTimeBetween('+1 month', '+6 months'),
                'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
                'updated_at' => function (array $attributes) {
                    return fake()->dateTimeBetween($attributes['created_at'], 'now');
                },
            ];
        }

        return [
            'work_order_id' => $workOrder->id,
            'asset_id' => $workOrder->asset_id,
            'performed_by' => $workOrder->technician_id,
            'maintenance_type' => $this->mapWorkOrderTypeToMaintenanceType($workOrder->type),
            'actions_taken' => fake()->paragraphs(3, true),
            'measurements' => json_encode(fake()->randomElements($measurements, fake()->numberBetween(2, 4))),
            'parts_used' => $workOrder->parts_used ?? json_encode($this->generatePartsUsed()),
            'time_spent_minutes' => $workOrder->time_spent_minutes ?? fake()->numberBetween(30, 300),
            'observations' => fake()->paragraph(),
            'attachments' => null,
            'result' => fake()->randomElement($results),
            'next_maintenance_date' => fake()->optional(0.6)->dateTimeBetween('+1 month', '+6 months'),
            'created_at' => $workOrder->completed_date ?? fake()->dateTimeBetween('-1 month', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Map work order type to valid maintenance type
     */
    private function mapWorkOrderTypeToMaintenanceType($workOrderType)
    {
        $mapping = [
            'preventive' => 'preventive',
            'corrective' => 'corrective',
            'emergency' => 'corrective', // Map emergency to corrective
            'inspection' => 'inspection',
        ];

        return $mapping[$workOrderType] ?? 'preventive';
    }

    /**
     * Generate parts used
     */
    private function generatePartsUsed()
    {
        $parts = [
            ['name' => 'Bearing 6304', 'quantity' => 2, 'part_number' => 'BRG-6304'],
            ['name' => 'Oil Seal', 'quantity' => 1, 'part_number' => 'SL-45-60'],
            ['name' => 'Grease', 'quantity' => 1, 'part_number' => 'GRS-LG2'],
            ['name' => 'Filter', 'quantity' => 1, 'part_number' => 'FIL-200'],
            ['name' => 'Capacitor', 'quantity' => 3, 'part_number' => 'CAP-450uF'],
            ['name' => 'Contactor', 'quantity' => 1, 'part_number' => 'CTC-9A'],
            ['name' => 'Fuse 10A', 'quantity' => 2, 'part_number' => 'FUS-10A'],
            ['name' => 'Terminal Block', 'quantity' => 4, 'part_number' => 'TB-4mm'],
        ];

        return fake()->randomElements($parts, fake()->numberBetween(0, 3));
    }

    /**
     * Indicate that the maintenance log is successful.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'successful',
        ]);
    }

    /**
     * Indicate that the maintenance log is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'failed',
        ]);
    }

    /**
     * Indicate that the maintenance log is preventive maintenance.
     */
    public function preventive(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_type' => 'preventive',
        ]);
    }

    /**
     * Indicate that the maintenance log is corrective maintenance.
     */
    public function corrective(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_type' => 'corrective',
        ]);
    }

    /**
     * Indicate that the maintenance log is an inspection.
     */
    public function inspection(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_type' => 'inspection',
        ]);
    }

    /**
     * Indicate that the maintenance log is a calibration.
     */
    public function calibration(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_type' => 'calibration',
        ]);
    }

    /**
     * Indicate that the maintenance log is a repair.
     */
    public function repair(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_type' => 'repair',
        ]);
    }

    /**
     * Set the asset for the maintenance log.
     */
    public function forAsset(Asset $asset): static
    {
        return $this->state(fn (array $attributes) => [
            'asset_id' => $asset->id,
        ]);
    }

    /**
     * Set the user who performed the maintenance.
     */
    public function performedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'performed_by' => $user->id,
        ]);
    }

    /**
     * Set the work order for the maintenance log.
     */
    public function forWorkOrder(WorkOrder $workOrder): static
    {
        return $this->state(fn (array $attributes) => [
            'work_order_id' => $workOrder->id,
            'asset_id' => $workOrder->asset_id,
            'performed_by' => $workOrder->technician_id,
            'maintenance_type' => $this->mapWorkOrderTypeToMaintenanceType($workOrder->type),
            'time_spent_minutes' => $workOrder->time_spent_minutes ?? fake()->numberBetween(30, 300),
            'parts_used' => $workOrder->parts_used,
            'created_at' => $workOrder->completed_date ?? now(),
        ]);
    }
}