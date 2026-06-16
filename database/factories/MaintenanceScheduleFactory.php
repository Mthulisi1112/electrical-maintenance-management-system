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
        $faker = $this->faker;

        $frequencies = ['daily', 'weekly', 'monthly', 'quarterly', 'semi_annual', 'annual'];
        $priorities = ['low', 'medium', 'high', 'critical'];

        $frequency = $faker->randomElement($frequencies);
        $startDate = $faker->dateTimeBetween('-6 months', '+3 months');

        $nextDueDate = $this->calculateNextDueDate(clone $startDate, $frequency);

        $checklistItems = [
            'Visual inspection',
            'Check vibration',
            'Measure temperature',
            'Check wiring',
            'Lubrication check',
            'Inspect corrosion',
            'Test safety systems',
            'Clean equipment',
            'Check alignment',
            'Insulation test',
        ];

        $tools = [
            'Multimeter',
            'Megger',
            'Thermal Camera',
            'Vibration Meter',
            'Tool Kit',
            'Lubrication Gun',
            'Oscilloscope',
            'Power Analyzer',
        ];

        return [
            'asset_id' => Asset::inRandomOrder()->first()?->id ?? Asset::factory(),

            'frequency' => $frequency,

            'title' => $faker->randomElement([
                'Routine Maintenance',
                'Preventive Maintenance',
                'Predictive Maintenance',
                'Condition-Based Maintenance'
            ]) . ': ' . $faker->words(3, true),

            'description' => $faker->paragraph(),

            'checklist_items' => json_encode(
                $faker->randomElements($checklistItems, rand(5, 8))
            ),

            'required_tools' => json_encode(
                $faker->randomElements($tools, rand(2, 5))
            ),

            'estimated_duration_minutes' => $faker->numberBetween(30, 480),

            'start_date' => $startDate,

            'next_due_date' => $nextDueDate,

            'last_completed_date' => $faker->optional(0.3)
                ->dateTimeBetween('-6 months', 'now'),

            'is_active' => $faker->boolean(90),

            'priority' => $faker->randomElement($priorities),

            // SAFE fallback instead of breaking factory dependency
            'created_by' => User::inRandomOrder()->first()?->id ?? User::factory(),

            'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    private function calculateNextDueDate($startDate, $frequency)
    {
        $date = clone $startDate;

        return match ($frequency) {
            'daily' => $date->modify('+1 day'),
            'weekly' => $date->modify('+1 week'),
            'monthly' => $date->modify('+1 month'),
            'quarterly' => $date->modify('+3 months'),
            'semi_annual' => $date->modify('+6 months'),
            'annual' => $date->modify('+1 year'),
            default => $date->modify('+1 month'),
        };
    }

    public function active(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function highPriority(): static
    {
        return $this->state(fn () => ['priority' => 'high']);
    }

    public function critical(): static
    {
        return $this->state(fn () => ['priority' => 'critical']);
    }

    public function daily(): static
    {
        return $this->state(fn () => [
            'frequency' => 'daily',
            'next_due_date' => now()->addDay(),
        ]);
    }

    public function weekly(): static
    {
        return $this->state(fn () => [
            'frequency' => 'weekly',
            'next_due_date' => now()->addWeek(),
        ]);
    }

    public function monthly(): static
    {
        return $this->state(fn () => [
            'frequency' => 'monthly',
            'next_due_date' => now()->addMonth(),
        ]);
    }
}