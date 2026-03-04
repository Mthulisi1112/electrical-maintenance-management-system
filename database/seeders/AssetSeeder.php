<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $adminUsers = User::whereHas('role', function($q) {
            $q->where('slug', 'admin');
        })->get();

        if ($adminUsers->isEmpty()) {
            $adminUsers = User::factory()->admin()->count(2)->create();
        }

        // Create motors
        Asset::factory()
            ->motor()
            ->count(50)
            ->create([
                'created_by' => $adminUsers->random()->id
            ]);

        // Create transformers
        Asset::factory()
            ->transformer()
            ->count(20)
            ->create([
                'created_by' => $adminUsers->random()->id
            ]);

        // Create VFDs
        Asset::factory()
            ->vfd()
            ->count(30)
            ->create([
                'created_by' => $adminUsers->random()->id
            ]);

        // Create other assets with various types
        $assetTypes = ['mcc', 'distribution_board', 'switchgear', 'cable', 'other'];
        foreach ($assetTypes as $type) {
            Asset::factory()
                ->count(15)
                ->create([
                    'type' => $type,
                    'created_by' => $adminUsers->random()->id
                ]);
        }

        // Create some faulty assets
        Asset::factory()
            ->faulty()
            ->count(10)
            ->create([
                'created_by' => $adminUsers->random()->id
            ]);

        // Create assets for specific locations
        $locations = ['Substation A', 'Substation B', 'Production Line 1', 'Production Line 2', 'Warehouse'];
        foreach ($locations as $location) {
            Asset::factory()
                ->count(5)
                ->create([
                    'location' => $location,
                    'created_by' => $adminUsers->random()->id
                ]);
        }
    }
}