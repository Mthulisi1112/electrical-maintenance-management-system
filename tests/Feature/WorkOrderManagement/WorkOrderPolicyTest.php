<?php

namespace Tests\Feature\WorkOrderManagement;

use Tests\TestCase;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;

class WorkOrderPolicyTest extends TestCase
{
    private WorkOrder $workOrder;
    private User $technician;
    private User $otherTechnician;
    private User $supervisor;
    private User $admin;
    private User $auditor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->technician = $this->createTechnician();
        $this->otherTechnician = $this->createTechnician();
        $this->supervisor = $this->createSupervisor();
        $this->admin = $this->createAdmin();
        $this->auditor = $this->createAuditor();
        
        $asset = Asset::factory()->create();
        $this->workOrder = WorkOrder::factory()->create([
            'asset_id' => $asset->id,
            'technician_id' => $this->technician->id,
            'supervisor_id' => $this->supervisor->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test viewAny policy - all authenticated users can access index
     */
    public function test_all_users_can_view_work_orders_index()
    {
        $this->assertTrue($this->technician->can('viewAny', WorkOrder::class));
        $this->assertTrue($this->otherTechnician->can('viewAny', WorkOrder::class));
        $this->assertTrue($this->supervisor->can('viewAny', WorkOrder::class));
        $this->assertTrue($this->admin->can('viewAny', WorkOrder::class));
        $this->assertTrue($this->auditor->can('viewAny', WorkOrder::class));
    }

    /**
     * Test view policy - technicians can only view their assigned work orders
     */
    public function test_technician_can_view_their_own_work_order()
    {
        $this->assertTrue($this->technician->can('view', $this->workOrder));
    }

    public function test_technician_cannot_view_others_work_order()
    {
        $this->assertFalse($this->otherTechnician->can('view', $this->workOrder));
    }

    public function test_supervisor_can_view_all_work_orders()
    {
        $this->assertTrue($this->supervisor->can('view', $this->workOrder));
    }

    public function test_admin_can_view_all_work_orders()
    {
        $this->assertTrue($this->admin->can('view', $this->workOrder));
    }

    public function test_auditor_can_view_all_work_orders()
    {
        $this->assertTrue($this->auditor->can('view', $this->workOrder));
    }

    /**
     * Test create policy - only admin and supervisor can create
     */
    public function test_admin_can_create_work_orders()
    {
        $this->assertTrue($this->admin->can('create', WorkOrder::class));
    }

    public function test_supervisor_can_create_work_orders()
    {
        $this->assertTrue($this->supervisor->can('create', WorkOrder::class));
    }

    public function test_technician_cannot_create_work_orders()
    {
        $this->assertFalse($this->technician->can('create', WorkOrder::class));
    }

    public function test_auditor_cannot_create_work_orders()
    {
        $this->assertFalse($this->auditor->can('create', WorkOrder::class));
    }

    /**
     * Test update policy
     */
    public function test_admin_can_update_any_work_order()
    {
        $this->assertTrue($this->admin->can('update', $this->workOrder));
    }

    public function test_supervisor_can_update_pending_work_order()
    {
        $this->assertTrue($this->supervisor->can('update', $this->workOrder));
    }

    public function test_supervisor_can_update_completed_work_order()
    {
        $this->workOrder->update(['status' => 'completed']);
        $this->assertTrue($this->supervisor->can('update', $this->workOrder));
    }

    public function test_supervisor_cannot_update_verified_work_order()
    {
        $this->workOrder->update(['status' => 'verified']);
        $this->assertFalse($this->supervisor->can('update', $this->workOrder));
    }

    public function test_technician_can_update_their_pending_work_order()
    {
        $this->assertTrue($this->technician->can('update', $this->workOrder));
    }

    public function test_technician_can_update_their_in_progress_work_order()
    {
        $this->workOrder->update(['status' => 'in_progress']);
        $this->assertTrue($this->technician->can('update', $this->workOrder));
    }

    public function test_technician_cannot_update_completed_work_order()
    {
        $this->workOrder->update(['status' => 'completed']);
        $this->assertFalse($this->technician->can('update', $this->workOrder));
    }

    public function test_technician_cannot_update_others_work_order()
    {
        $this->assertFalse($this->otherTechnician->can('update', $this->workOrder));
    }

    public function test_auditor_cannot_update_any_work_order()
    {
        $this->assertFalse($this->auditor->can('update', $this->workOrder));
    }

    /**
     * Test delete policy - only admin can delete
     */
    public function test_admin_can_delete_work_orders()
    {
        $this->assertTrue($this->admin->can('delete', $this->workOrder));
    }

    public function test_supervisor_cannot_delete_work_orders()
    {
        $this->assertFalse($this->supervisor->can('delete', $this->workOrder));
    }

    public function test_technician_cannot_delete_work_orders()
    {
        $this->assertFalse($this->technician->can('delete', $this->workOrder));
    }

    public function test_auditor_cannot_delete_work_orders()
    {
        $this->assertFalse($this->auditor->can('delete', $this->workOrder));
    }

    /**
     * Test verify policy
     */
    public function test_admin_can_verify_completed_work_orders()
    {
        $this->workOrder->update(['status' => 'completed']);
        $this->assertTrue($this->admin->can('verify', $this->workOrder));
    }

    public function test_supervisor_can_verify_completed_work_orders()
    {
        $this->workOrder->update(['status' => 'completed']);
        $this->assertTrue($this->supervisor->can('verify', $this->workOrder));
    }

    public function test_technician_cannot_verify_work_orders()
    {
        $this->workOrder->update(['status' => 'completed']);
        $this->assertFalse($this->technician->can('verify', $this->workOrder));
    }

    public function test_cannot_verify_non_completed_work_orders()
    {
        $this->assertFalse($this->supervisor->can('verify', $this->workOrder)); // status is pending
    }

    /**
     * Test verifyAny policy (for dashboard)
     */
    public function test_admin_can_verify_any_work_order()
    {
        $this->assertTrue($this->admin->can('verifyAny', WorkOrder::class));
    }

    public function test_supervisor_can_verify_any_work_order()
    {
        $this->assertTrue($this->supervisor->can('verifyAny', WorkOrder::class));
    }

    public function test_technician_cannot_verify_any_work_order()
    {
        $this->assertFalse($this->technician->can('verifyAny', WorkOrder::class));
    }
}