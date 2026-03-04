<?php

namespace Tests\Feature\WorkOrderManagement;

use Tests\TestCase;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;

class WorkOrderCrudTest extends TestCase
{
    public function test_supervisor_can_create_work_order()
    {
        $supervisor = $this->createSupervisor();
        $asset = Asset::factory()->create();
        $technician = $this->createTechnician();

        $response = $this->actingAs($supervisor)->post('/work-orders', [
            'asset_id' => $asset->id,
            'technician_id' => $technician->id,
            'type' => 'preventive',
            'title' => 'Test Work Order',
            'description' => 'Test Description',
            'scheduled_date' => now()->format('Y-m-d'),
            'priority' => 'medium',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('work_orders', [
            'title' => 'Test Work Order',
            'supervisor_id' => $supervisor->id,
        ]);
    }

    public function test_technician_cannot_create_work_order()
    {
        $technician = $this->createTechnician();
        $asset = Asset::factory()->create();

        $response = $this->actingAs($technician)->post('/work-orders', [
            'asset_id' => $asset->id,
            'technician_id' => $technician->id,
            'type' => 'preventive',
            'title' => 'Test Work Order',
            'description' => 'Test Description',
            'scheduled_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(403);
    }

    public function test_technician_can_view_their_assigned_work_orders()
    {
        // Create technicians for this test
        $technician = $this->createTechnician();
        $otherTechnician = $this->createTechnician();
        
        $asset = Asset::factory()->create();
        
        // Create a work order assigned to our technician
        $myWorkOrder = WorkOrder::factory()->create([
            'technician_id' => $technician->id,
            'asset_id' => $asset->id,
            'title' => 'Routine Maintenance: et accusamus laborum',
        ]);
        
        // Create a work order assigned to another technician
        $otherWorkOrder = WorkOrder::factory()->create([
            'technician_id' => $otherTechnician->id,
            'asset_id' => $asset->id,
            'title' => 'Other Technician Work Order',
        ]);

        // Act as our technician and visit the work orders page
        $response = $this->actingAs($technician)->get('/work-orders');
        
        // Should see their own work order title (or partial due to truncation)
        $response->assertSee('Routine Maintenance: et');
        $response->assertSee($myWorkOrder->work_order_number);
        
        // Should NOT see the other technician's work order
        $response->assertDontSee('Other Technician Work Order');
        $response->assertDontSee($otherWorkOrder->work_order_number);
    }

    public function test_work_order_number_is_generated_automatically()
    {
        $supervisor = $this->createSupervisor();
        $asset = Asset::factory()->create();
        $technician = $this->createTechnician();

        $workOrder = WorkOrder::create([
            'asset_id' => $asset->id,
            'technician_id' => $technician->id,
            'supervisor_id' => $supervisor->id,
            'type' => 'preventive',
            'status' => 'pending',
            'title' => 'Test',
            'description' => 'Test',
            'scheduled_date' => now(),
        ]);

        $this->assertNotNull($workOrder->work_order_number);
        $this->assertStringStartsWith('WO-', $workOrder->work_order_number);
    }
}