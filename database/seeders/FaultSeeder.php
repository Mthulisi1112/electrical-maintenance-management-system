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
        $technicians = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->get();

        $reporters = User::all();

        if ($technicians->isEmpty()) {
            $technicians = User::factory()->technician()->count(10)->create();
        }

        $assets = Asset::all();

        // Create reported faults
        Fault::factory()
            ->reported()
            ->count(20)
            ->create()
            ->each(function ($fault) use ($assets, $reporters) {
                $fault->asset_id = $assets->random()->id;
                $fault->reported_by = $reporters->random()->id;
                $fault->save();
            });

        // Create investigating faults
        Fault::factory()
            ->investigating()
            ->count(15)
            ->create()
            ->each(function ($fault) use ($assets, $reporters, $technicians) {
                $fault->asset_id = $assets->random()->id;
                $fault->reported_by = $reporters->random()->id;
                $fault->assigned_to = $technicians->random()->id;
                $fault->save();
            });

        // Create resolved faults
        Fault::factory()
            ->resolved()
            ->count(40)
            ->create()
            ->each(function ($fault) use ($assets, $reporters, $technicians) {
                $fault->asset_id = $assets->random()->id;
                $fault->reported_by = $reporters->random()->id;
                $fault->assigned_to = $technicians->random()->id;
                $fault->save();
            });

        // Create critical faults
        Fault::factory()
            ->critical()
            ->count(10)
            ->create()
            ->each(function ($fault) use ($assets, $reporters, $technicians) {
                $fault->asset_id = $assets->random()->id;
                $fault->reported_by = $reporters->random()->id;
                $fault->assigned_to = $technicians->random()->id;
                $fault->save();
            });

        // Create high severity faults
        Fault::factory()
            ->high()
            ->count(15)
            ->create()
            ->each(function ($fault) use ($assets, $reporters, $technicians) {
                $fault->asset_id = $assets->random()->id;
                $fault->reported_by = $reporters->random()->id;
                $fault->assigned_to = $technicians->random()->id;
                $fault->save();
            });

        // Create faults requiring follow-up
        Fault::factory()
            ->requiresFollowup()
            ->count(8)
            ->create()
            ->each(function ($fault) use ($assets, $reporters, $technicians) {
                $fault->asset_id = $assets->random()->id;
                $fault->reported_by = $reporters->random()->id;
                $fault->assigned_to = $technicians->random()->id;
                $fault->save();
            });

        // Create faults for specific assets
        $faultProneAssets = Asset::where('status', 'faulty')->take(15)->get();
        foreach ($faultProneAssets as $asset) {
            Fault::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create([
                    'asset_id' => $asset->id,
                    'reported_by' => $reporters->random()->id,
                    'assigned_to' => $technicians->random()->id,
                ]);
        }

        // Create historical faults
        Fault::factory()
            ->count(60)
            ->create([
                'created_at' => fake()->dateTimeBetween('-1 year', '-1 month'),
                'downtime_start' => fake()->dateTimeBetween('-1 year', '-1 month'),
                'downtime_end' => fake()->dateTimeBetween('-11 months', '-1 month'),
            ])
            ->each(function ($fault) use ($assets, $reporters, $technicians) {
                $fault->asset_id = $assets->random()->id;
                $fault->reported_by = $reporters->random()->id;
                $fault->assigned_to = $technicians->random()->id;
                $fault->status = 'closed';
                $fault->save();
            });
    }
}