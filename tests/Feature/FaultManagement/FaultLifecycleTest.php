<?php

namespace Tests\Feature\FaultManagement;

use Tests\TestCase;
use App\Models\Fault;
use App\Models\Asset;
use App\Models\User;

class FaultLifecycleTest extends TestCase
{
    private Fault $fault;
    private User $technician;
    private User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->technician = $this->createTechnician();
        $this->supervisor = $this->createSupervisor();
        $asset = Asset::factory()->create();
        
        $this->fault = Fault::factory()->create([
            'asset_id' => $asset->id,
            'reported_by' => $this->technician->id,
            'status' => 'reported',
            'downtime_start' => now(),
        ]);
    }

    public function test_supervisor_can_assign_fault_to_technician()
    {
        $otherTechnician = $this->createTechnician();

        $response = $this->actingAs($this->supervisor)
            ->post("/faults/{$this->fault->id}/assign", [
                'assigned_to' => $otherTechnician->id,
            ]);

        $response->assertStatus(302);
        $this->fault->refresh();
        $this->assertEquals($otherTechnician->id, $this->fault->assigned_to);
        $this->assertEquals('investigating', $this->fault->status);
    }

    public function test_assigned_technician_can_resolve_fault()
    {
        $this->fault->update([
            'assigned_to' => $this->technician->id,
            'status' => 'investigating',
        ]);

        $response = $this->actingAs($this->technician)
            ->post("/faults/{$this->fault->id}/resolve", [
                'root_cause' => 'Worn bearing',
                'corrective_actions' => 'Replaced bearing',
                'parts_replaced' => [
                    ['name' => 'Bearing 6304', 'quantity' => 2, 'part_number' => 'BRG-6304']
                ],
            ]);

        $response->assertStatus(302);
        $this->fault->refresh();
        $this->assertEquals('resolved', $this->fault->status);
        $this->assertEquals('Worn bearing', $this->fault->root_cause);
        $this->assertNotNull($this->fault->downtime_end);
        
        // Asset status should return to operational
        $this->fault->asset->refresh();
        $this->assertEquals('operational', $this->fault->asset->status);
    }

    public function test_cannot_resolve_fault_without_root_cause()
    {
        $this->fault->update([
            'assigned_to' => $this->technician->id,
            'status' => 'investigating',
        ]);

        $response = $this->actingAs($this->technician)
            ->post("/faults/{$this->fault->id}/resolve", [
                // Missing root_cause
                'corrective_actions' => 'Fixed it',
            ]);

        $response->assertSessionHasErrors(['root_cause']);
    }
}