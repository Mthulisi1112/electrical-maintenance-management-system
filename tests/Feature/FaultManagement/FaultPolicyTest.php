<?php

namespace Tests\Feature\FaultManagement;

use Tests\TestCase;
use App\Models\Fault;
use App\Models\Asset;
use App\Models\User;

class FaultPolicyTest extends TestCase
{
    private Fault $fault;
    private User $reporter;
    private User $otherTechnician;
    private User $supervisor;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reporter = $this->createTechnician();
        $this->otherTechnician = $this->createTechnician();
        $this->supervisor = $this->createSupervisor();
        $this->admin = $this->createAdmin();
        
        $asset = Asset::factory()->create();
        $this->fault = Fault::factory()->create([
            'asset_id' => $asset->id,
            'reported_by' => $this->reporter->id,
            'status' => 'reported',
        ]);
    }

    public function test_reporter_can_view_their_fault()
    {
        $this->assertTrue($this->reporter->can('view', $this->fault));
    }

    public function test_other_technician_cannot_view_unassigned_fault()
    {
        // Create a fault that belongs to our main technician
        // The fault is NOT assigned to the other technician
        $this->assertFalse($this->otherTechnician->can('view', $this->fault));
    }

    public function test_assigned_technician_can_view_fault()
    {
        $this->fault->update(['assigned_to' => $this->otherTechnician->id]);
        $this->assertTrue($this->otherTechnician->can('view', $this->fault));
    }

    public function test_supervisor_can_view_all_faults()
    {
        $this->assertTrue($this->supervisor->can('view', $this->fault));
    }

    public function test_admin_can_view_all_faults()
    {
        $this->assertTrue($this->admin->can('view', $this->fault));
    }

    public function test_technician_can_create_fault()
    {
        $this->assertTrue($this->reporter->can('create', Fault::class));
    }

    public function test_auditor_cannot_create_fault()
    {
        $auditor = $this->createAuditor();
        $this->assertFalse($auditor->can('create', Fault::class));
    }

    public function test_supervisor_can_assign_fault()
    {
        $this->assertTrue($this->supervisor->can('assign', $this->fault));
    }

    public function test_technician_cannot_assign_fault()
    {
        $this->assertFalse($this->reporter->can('assign', $this->fault));
    }

    public function test_assigned_technician_can_resolve_fault()
    {
        $this->fault->update(['assigned_to' => $this->reporter->id]);
        $this->assertTrue($this->reporter->can('resolve', $this->fault));
    }

    public function test_unassigned_technician_cannot_resolve_fault()
    {
        $this->assertFalse($this->otherTechnician->can('resolve', $this->fault));
    }
}