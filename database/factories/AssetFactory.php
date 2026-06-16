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
        $faker = $this->faker;

        $types = ['motor', 'transformer', 'mcc', 'distribution_board', 'vfd', 'switchgear', 'cable', 'other'];
        $type = $faker->randomElement($types);

        $statuses = ['operational', 'maintenance', 'faulty', 'decommissioned'];

        $voltages = [110, 220, 380, 415, 480, 1100, 3300, 6600, 11000];
        $currents = [10, 20, 50, 100, 200, 400, 800, 1600];
        $powers = [0.75, 1.5, 2.2, 3.7, 5.5, 7.5, 11, 15, 18.5, 22, 30, 37, 45, 55, 75, 90, 110, 132, 160, 200, 250, 315, 355, 400];

        $createdAt = $faker->dateTimeBetween('-1 year', 'now');

        return [
            'asset_code' => $faker->unique()->regexify('[A-Z]{3}-[0-9]{4}-[A-Z]{2}'),

            'type' => $type,

            'name' => $faker->words(3, true) . ' ' . $faker->randomElement([
                'Motor', 'Pump', 'Fan', 'Compressor', 'Conveyor'
            ]),

            'location' => $faker->randomElement([
                'Building A', 'Building B', 'Substation 1', 'Substation 2',
                'Production Line 1', 'Production Line 2', 'Warehouse', 'Control Room'
            ]),

            'manufacturer' => $faker->randomElement([
                'Siemens', 'ABB', 'Schneider Electric', 'GE', 'Eaton',
                'Rockwell', 'Mitsubishi', 'Fuji', 'WEG', 'Toshiba'
            ]),

            'model' => $faker->bothify('??-####-???'),

            'serial_number' => $faker->unique()->bothify('SN-####-????-####'),

            'voltage_rating' => $faker->randomElement($voltages),
            'current_rating' => $faker->randomElement($currents),
            'power_rating' => $faker->randomElement($powers),

            'installation_date' => $faker->dateTimeBetween('-10 years', 'now'),

            'status' => $faker->randomElement($statuses),

            'technical_specs' => json_encode([
                'ip_rating' => $faker->randomElement(['IP54', 'IP55', 'IP65', 'IP66', 'IP67']),
                'insulation_class' => $faker->randomElement(['F', 'H', 'B']),
                'duty_cycle' => $faker->randomElement(['S1', 'S3', 'S6']),
                'mounting' => $faker->randomElement(['Foot', 'Flange', 'Vertical']),
                'bearings' => $faker->randomElement(['6304', '6205', '6306', 'NU204']),
            ]),

            'qr_code' => null,

            'created_by' => User::factory(),

            'created_at' => $createdAt,

            'updated_at' => $faker->dateTimeBetween($createdAt, 'now'),
        ];
    }

    public function operational(): static
    {
        return $this->state(fn () => [
            'status' => 'operational',
        ]);
    }

    public function faulty(): static
    {
        return $this->state(fn () => [
            'status' => 'faulty',
        ]);
    }

    public function motor(): static
    {
        return $this->state(fn () => [
            'type' => 'motor',
            'name' => $this->faker->randomElement([
                'Induction Motor', 'Synchronous Motor', 'DC Motor'
            ]) . ' ' . $this->faker->randomNumber(4),
        ]);
    }

    public function transformer(): static
    {
        return $this->state(fn () => [
            'type' => 'transformer',
            'name' => $this->faker->randomElement([
                'Distribution Transformer',
                'Power Transformer',
                'Isolation Transformer'
            ]) . ' ' . $this->faker->randomNumber(3) . 'kVA',
        ]);
    }

    public function vfd(): static
    {
        return $this->state(fn () => [
            'type' => 'vfd',
            'name' => 'Variable Frequency Drive ' . $this->faker->randomNumber(4),
        ]);
    }
}