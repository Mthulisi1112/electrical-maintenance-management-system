<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $types = ['motor', 'transformer', 'mcc', 'distribution_board', 'vfd', 'switchgear', 'cable', 'other'];
        $type = fake()->randomElement($types);
        $statuses = ['operational', 'maintenance', 'faulty', 'decommissioned'];
        
        $voltages = [110, 220, 380, 415, 480, 1100, 3300, 6600, 11000];
        $currents = [10, 20, 50, 100, 200, 400, 800, 1600];
        $powers = [0.75, 1.5, 2.2, 3.7, 5.5, 7.5, 11, 15, 18.5, 22, 30, 37, 45, 55, 75, 90, 110, 132, 160, 200, 250, 315, 355, 400];

        return [
            'asset_code' => fake()->unique()->regexify('[A-Z]{3}-[0-9]{4}-[A-Z]{2}'),
            'type' => $type,
            'name' => fake()->words(3, true) . ' ' . fake()->randomElement(['Motor', 'Pump', 'Fan', 'Compressor', 'Conveyor']),
            'location' => fake()->randomElement(['Building A', 'Building B', 'Substation 1', 'Substation 2', 'Production Line 1', 'Production Line 2', 'Warehouse', 'Control Room']),
            'manufacturer' => fake()->randomElement(['Siemens', 'ABB', 'Schneider Electric', 'GE', 'Eaton', 'Rockwell', 'Mitsubishi', 'Fuji', 'WEG', 'Toshiba']),
            'model' => fake()->bothify('??-####-???'),
            'serial_number' => fake()->unique()->bothify('SN-####-????-####'),
            'voltage_rating' => fake()->randomElement($voltages),
            'current_rating' => fake()->randomElement($currents),
            'power_rating' => fake()->randomElement($powers),
            'installation_date' => fake()->dateTimeBetween('-10 years', 'now'),
            'status' => fake()->randomElement($statuses),
            'technical_specs' => json_encode([
                'ip_rating' => fake()->randomElement(['IP54', 'IP55', 'IP65', 'IP66', 'IP67']),
                'insulation_class' => fake()->randomElement(['F', 'H', 'B']),
                'duty_cycle' => fake()->randomElement(['S1', 'S3', 'S6']),
                'mounting' => fake()->randomElement(['Foot', 'Flange', 'Vertical']),
                'bearings' => fake()->randomElement(['6304', '6205', '6306', 'NU204']),
            ]),
            'qr_code' => null,
            'created_by' => User::factory(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    public function operational(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'operational',
        ]);
    }

    public function faulty(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'faulty',
        ]);
    }

    public function motor(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'motor',
            'name' => fake()->randomElement(['Induction Motor', 'Synchronous Motor', 'DC Motor']) . ' ' . fake()->randomNumber(4),
        ]);
    }

    public function transformer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'transformer',
            'name' => fake()->randomElement(['Distribution Transformer', 'Power Transformer', 'Isolation Transformer']) . ' ' . fake()->randomNumber(3) . 'kVA',
        ]);
    }

    public function vfd(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'vfd',
            'name' => 'Variable Frequency Drive ' . fake()->randomNumber(4),
        ]);
    }
}