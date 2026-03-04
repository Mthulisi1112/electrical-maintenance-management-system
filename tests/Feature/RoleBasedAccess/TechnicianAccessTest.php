<?php

namespace Tests\Feature\RoleBasedAccess;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\WorkOrder;
use App\Models\Fault;
use App\Models\MaintenanceSchedule;
use App\Models\User;

class TechnicianAccessTest extends TestCase
{
    private User $technician;
    private User $otherTechnician;

    protected function setUp(): void
    {
        parent::setUp();
        $this->technician = $this->createTechnician();
        $this->otherTechnician = $this->createTechnician();
    }

    public function test_technician_can_view_assets_and_schedules()
    {
        $response = $this->actingAs($this->technician)->get('/assets');
        $response->assertStatus(200);

        $response = $this->actingAs($this->technician)->get('/maintenance-schedules');
        $response->assertStatus(200);
    }

    public function test_technician_cannot_create_assets()
    {
        $response = $this->actingAs($this->technician)->get('/assets/create');
        $response->assertStatus(403);
    }

    public function test_technician_can_view_their_assigned_work_orders()
    {
        $asset = Asset::factory()->create();
        
        $myWorkOrder = WorkOrder::factory()->create([
            'technician_id' => $this->technician->id,
            'asset_id' => $asset->id,
            'title' => 'Routine Maintenance: et accusamus laborum',
        ]);
        
        $otherWorkOrder = WorkOrder::factory()->create([
            'technician_id' => $this->otherTechnician->id,
            'asset_id' => $asset->id,
            'title' => 'Other Technician Work Order',
        ]);

        $response = $this->actingAs($this->technician)->get('/work-orders');
        
        // Look for the beginning of the title (before truncation)
        $response->assertSee('Routine Maintenance: et');
        $response->assertDontSee('Other Technician Work Order');
    }

    public function test_technician_can_start_and_complete_their_work_orders()
    {
        $workOrder = WorkOrder::factory()->create([
            'technician_id' => $this->technician->id,
            'status' => 'pending',
        ]);

        $this->assertTrue($this->technician->can('update', $workOrder));
        
        $workOrder->update(['status' => 'in_progress']);
        $this->assertTrue($this->technician->can('update', $workOrder));
    }

    public function test_technician_can_report_faults()
    {
        $response = $this->actingAs($this->technician)->get('/faults/create');
        $response->assertStatus(200);
    }

    public function test_technician_can_view_their_reported_faults()
    {
        $asset = Asset::factory()->create();
        
        $myFault = Fault::factory()->create([
            'reported_by' => $this->technician->id,
            'asset_id' => $asset->id,
        ]);
        
        $otherFault = Fault::factory()->create([
            'reported_by' => $this->otherTechnician->id,
            'asset_id' => $asset->id,
        ]);

        $response = $this->actingAs($this->technician)->get('/faults');
        $response->assertSee($myFault->fault_number);
        $response->assertDontSee($otherFault->fault_number);
    }

    public function test_technician_cannot_access_user_management()
    {
        $response = $this->actingAs($this->technician)->get('/admin/users');
        $response->assertStatus(403);
    }
}