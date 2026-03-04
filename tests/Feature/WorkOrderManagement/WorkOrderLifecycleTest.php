<?php

namespace Tests\Feature\WorkOrderManagement;

use Tests\TestCase;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;

class WorkOrderLifecycleTest extends TestCase
{
    private WorkOrder $workOrder;
    private User $technician;
    private User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->technician = $this->createTechnician();
        $this->supervisor = $this->createSupervisor();
        $asset = Asset::factory()->create();
        
        $this->workOrder = WorkOrder::factory()->create([
            'asset_id' => $asset->id,
            'technician_id' => $this->technician->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 'pending',
        ]);
    }

    public function test_technician_can_start_work_order()
    {
        $response = $this->actingAs($this->technician)
            ->post("/work-orders/{$this->workOrder->id}/start");

        $response->assertStatus(302);
        $this->workOrder->refresh();
        $this->assertEquals('in_progress', $this->workOrder->status);
        $this->assertNotNull($this->workOrder->started_at);
    }

    public function test_technician_can_complete_work_order()
    {
        $technician = $this->createTechnician();
        $workOrder = WorkOrder::factory()->create([
            'technician_id' => $technician->id,
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($technician)
            ->post("/work-orders/{$workOrder->id}/complete", [
                'time_spent_minutes' => 120,
                'actions_taken' => 'Performed maintenance tasks',
                'technician_remarks' => 'All good',
                'measurements' => [
                    ['name' => 'Temperature', 'value' => 45, 'unit' => 'C']
                ],
            ]);

        $response->assertStatus(302);
        $workOrder->refresh();
        $this->assertEquals('completed', $workOrder->status);
        $this->assertEquals(120, $workOrder->time_spent_minutes);
    }

    public function test_supervisor_can_verify_completed_work_order()
    {
        $this->workOrder->update([
            'status' => 'completed',
            'completed_date' => now(),
        ]);

        $response = $this->actingAs($this->supervisor)
            ->post("/work-orders/{$this->workOrder->id}/verify", [
                'supervisor_remarks' => 'Work verified',
            ]);

        $response->assertStatus(302);
        $this->workOrder->refresh();
        $this->assertEquals('verified', $this->workOrder->status);
        $this->assertNotNull($this->workOrder->verified_at);
    }

    public function test_technician_cannot_verify_work_order()
    {
        $this->workOrder->update(['status' => 'completed']);

        $response = $this->actingAs($this->technician)
            ->post("/work-orders/{$this->workOrder->id}/verify");

        $response->assertStatus(403);
        $this->workOrder->refresh();
        $this->assertEquals('completed', $this->workOrder->status);
    }

    public function test_cannot_complete_work_order_without_required_fields()
    {
        $this->workOrder->update(['status' => 'in_progress']);

        $response = $this->actingAs($this->technician)
            ->post("/work-orders/{$this->workOrder->id}/complete", [
                // Missing required fields
            ]);

        $response->assertSessionHasErrors(['time_spent_minutes', 'actions_taken']);
    }
}