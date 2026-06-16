<?php

namespace Database\Seeders;

use App\Models\Fault;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class FaultSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $technicians = User::whereHas('role', function ($q) {
            $q->where('slug', 'technician');
        })->get();

        $reporters = User::all();

        if ($technicians->isEmpty()) {
            $technicians = User::factory()->technician()->count(10)->create();
        }

        $assets = Asset::all();

        if ($assets->isEmpty()) {
            $assets = Asset::factory()->count(20)->create();
        }

        if ($reporters->isEmpty()) {
            $reporters = User::factory()->count(10)->create();
        }

        // Helper closure to avoid repetition
        $assignRelations = function ($fault) use ($assets, $reporters, $technicians) {
            $fault->update([
                'asset_id' => $assets->random()->id,
                'reported_by' => $reporters->random()->id,
                'assigned_to' => $technicians->random()->id,
            ]);
        };

        Fault::factory()->reported()->count(20)->create()->each($assignRelations);

        Fault::factory()->investigating()->count(15)->create()->each($assignRelations);

        Fault::factory()->resolved()->count(40)->create()->each($assignRelations);

        Fault::factory()->critical()->count(10)->create()->each($assignRelations);

        Fault::factory()->high()->count(15)->create()->each($assignRelations);

        Fault::factory()->requiresFollowup()->count(8)->create()->each($assignRelations);

        // Fault-prone assets
        $faultProneAssets = Asset::where('status', 'faulty')->take(15)->get();

        foreach ($faultProneAssets as $asset) {
            Fault::factory()
                ->count($faker->numberBetween(1, 3))
                ->create([
                    'asset_id' => $asset->id,
                    'reported_by' => $reporters->random()->id ?? null,
                    'assigned_to' => $technicians->random()->id ?? null,
                ]);
        }

        // Historical faults
        Fault::factory()
            ->count(60)
            ->create([
                'created_at' => $faker->dateTimeBetween('-1 year', '-1 month'),
                'downtime_start' => $faker->dateTimeBetween('-1 year', '-1 month'),
                'downtime_end' => $faker->dateTimeBetween('-11 months', '-1 month'),
                'status' => 'closed',
            ])
            ->each($assignRelations);
    }
}