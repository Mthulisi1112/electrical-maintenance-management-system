<?php

namespace Database\Factories;

use App\Models\MaintenanceSchedule;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceScheduleFactory extends Factory
{
    protected $model = MaintenanceSchedule::class;

    public function definition(): array
    {
        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'];
        $frequency = fake()->randomElement($frequencies);
        $priorities = ['low', 'medium', 'high', 'critical'];
        
        $startDate = fake()->dateTimeBetween('-6 months', '+3 months');
        $nextDueDate = $this->calculateNextDueDate($startDate, $frequency);
        
        $checklistItems = [
            'Visual inspection of equipment',
            'Check for unusual noise or vibration',
            'Measure operating temperature',
            'Check electrical connections',
            'Verify proper lubrication',
            'Inspect for corrosion or damage',
            'Test safety devices',
            'Verify proper operation',
            'Clean equipment surfaces',
            'Check alignment',
            'Measure insulation resistance',
            'Check cooling system',
        ];

        return [
            'asset_id' => Asset::factory(),
            'frequency' => $frequency,
            'title' => fake()->randomElement(['Routine', 'Preventive', 'Predictive', 'Condition-based']) . ' Maintenance: ' . fake()->words(3, true),
            'description' => fake()->paragraphs(3, true),
            'checklist_items' => json_encode(fake()->randomElements($checklistItems, fake()->numberBetween(5, 10))),
            'required_tools' => json_encode(fake()->randomElements(['Multimeter', 'Megger', 'Thermal Camera', 'Vibration Meter', 'Tool Set', 'Lubrication Gun', 'Oscilloscope', 'Power Analyzer'], fake()->numberBetween(2, 5))),
            'estimated_duration_minutes' => fake()->numberBetween(30, 480),
            'start_date' => $startDate,
            'next_due_date' => $nextDueDate,
            'last_completed_date' => fake()->optional(0.3)->dateTimeBetween('-6 months', 'now'),
            'is_active' => fake()->boolean(90),
            'priority' => fake()->randomElement($priorities),
            'created_by' => User::factory(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    private function calculateNextDueDate($startDate, $frequency)
    {
        $date = clone $startDate;
        
        switch ($frequency) {
            case 'daily':
                return $date->modify('+1 day');
            case 'weekly':
                return $date->modify('+1 week');
            case 'monthly':
                return $date->modify('+1 month');
            case 'quarterly':
                return $date->modify('+3 months');
            case 'semi_annual':
                return $date->modify('+6 months');
            case 'annual':
                return $date->modify('+1 year');
            default:
                return $date->modify('+1 month');
        }
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'critical',
        ]);
    }

    public function daily(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'daily',
            'next_due_date' => now()->addDay(),
        ]);
    }

    public function weekly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'weekly',
            'next_due_date' => now()->addWeek(),
        ]);
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'monthly',
            'next_due_date' => now()->addMonth(),
        ]);
    }
}