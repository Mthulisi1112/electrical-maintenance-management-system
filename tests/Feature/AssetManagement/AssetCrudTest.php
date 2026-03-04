<?php

namespace Tests\Feature\AssetManagement;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;

class AssetCrudTest extends TestCase
{
    public function test_admin_can_create_asset()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/assets', [
            'asset_code' => 'MTR-001',
            'name' => 'Test Motor',
            'type' => 'motor',
            'location' => 'Building A',
            'installation_date' => now()->format('Y-m-d'),
            'manufacturer' => 'Siemens',
            'voltage_rating' => 415,
            'power_rating' => 75,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('assets', [
            'asset_code' => 'MTR-001',
            'name' => 'Test Motor',
        ]);
    }

    public function test_technician_cannot_create_asset()
    {
        $technician = $this->createTechnician();

        $response = $this->actingAs($technician)->post('/assets', [
            'asset_code' => 'MTR-002',
            'name' => 'Another Motor',
            'type' => 'motor',
            'location' => 'Building B',
            'installation_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseMissing('assets', ['asset_code' => 'MTR-002']);
    }

    public function test_user_can_view_assets_list()
    {
        $user = $this->createTechnician();
        Asset::factory()->count(5)->create();

        $response = $this->actingAs($user)->get('/assets');

        $response->assertStatus(200);
        $response->assertViewHas('assets');
    }

    public function test_admin_can_update_asset()
    {
        $admin = $this->createAdmin();
        $asset = Asset::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->put("/assets/{$asset->id}", [
            'name' => 'Updated Name',
            'asset_code' => $asset->asset_code,
            'type' => $asset->type,
            'location' => $asset->location,
            'installation_date' => $asset->installation_date->format('Y-m-d'),
            'status' => $asset->status, // Make sure to include status
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_admin_can_delete_asset()
    {
        $admin = $this->createAdmin();
        $asset = Asset::factory()->create();

        $response = $this->actingAs($admin)->delete("/assets/{$asset->id}");

        $response->assertStatus(302);
        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
    }
}