<?php

namespace Tests\Feature\FaultManagement;

use Tests\TestCase;
use App\Models\Fault;
use App\Models\Asset;

class FaultCrudTest extends TestCase
{
    public function test_technician_can_report_fault()
    {
        $technician = $this->createTechnician();
        $asset = Asset::factory()->create();

        $response = $this->actingAs($technician)->post('/faults', [
            'asset_id' => $asset->id,
            'fault_type' => 'trip',
            'severity' => 'high',
            'description' => 'Motor tripped unexpectedly',
            'symptoms' => ['Unusual noise', 'Overheating'],
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('faults', [
            'asset_id' => $asset->id,
            'reported_by' => $technician->id,
            'description' => 'Motor tripped unexpectedly',
            'status' => 'reported',
        ]);
    }

    public function test_fault_number_is_generated_automatically()
    {
        $technician = $this->createTechnician();
        $asset = Asset::factory()->create();

        $fault = Fault::create([
            'asset_id' => $asset->id,
            'reported_by' => $technician->id,
            'fault_type' => 'trip',
            'severity' => 'high',
            'description' => 'Test fault',
            'downtime_start' => now(),
        ]);

        $this->assertNotNull($fault->fault_number);
        $this->assertStringStartsWith('FLT-', $fault->fault_number);
    }

    public function test_asset_status_updates_to_faulty_when_fault_reported()
    {
        $technician = $this->createTechnician();
        $asset = Asset::factory()->create(['status' => 'operational']);

        $this->actingAs($technician)->post('/faults', [
            'asset_id' => $asset->id,
            'fault_type' => 'trip',
            'severity' => 'high',
            'description' => 'Test fault',
        ]);

        $asset->refresh();
        $this->assertEquals('faulty', $asset->status);
    }
}