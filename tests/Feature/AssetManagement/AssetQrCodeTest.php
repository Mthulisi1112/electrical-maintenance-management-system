<?php

namespace Tests\Feature\AssetManagement;

use Tests\TestCase;
use App\Models\Asset;

class AssetQrCodeTest extends TestCase
{
    public function test_asset_has_qr_code_generation_method()
    {
        $asset = Asset::factory()->create(['qr_code' => null]);
        
        $asset->generateQrCode();
        
        $this->assertNotNull($asset->qr_code);
        $this->assertStringStartsWith('ASSET-' . $asset->id, $asset->qr_code);
    }

    public function test_qr_code_route_returns_image()
    {
        $user = $this->createTechnician();
        $asset = Asset::factory()->create();

        $response = $this->actingAs($user)->get("/assets/{$asset->id}/qrcode");

        $response->assertStatus(200);
        $response->assertHeader('Content-type', 'image/svg+xml');
    }

    public function test_qr_code_can_be_downloaded()
    {
        $user = $this->createTechnician();
        $asset = Asset::factory()->create();

        $response = $this->actingAs($user)->get("/assets/{$asset->id}/qrcode?download=1");

        $response->assertStatus(200);
        $response->assertHeader('Content-type', 'image/svg+xml');
        $response->assertHeader('Content-Disposition', "attachment; filename=\"qrcode-{$asset->asset_code}.svg\"");
    }
}