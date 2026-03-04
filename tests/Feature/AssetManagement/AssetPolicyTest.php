<?php

namespace Tests\Feature\AssetManagement;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;

class AssetPolicyTest extends TestCase
{
    private Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->asset = Asset::factory()->create();
    }

    public function test_admin_can_delete_asset()
    {
        $admin = $this->createAdmin();
        $this->assertTrue($admin->can('delete', $this->asset));
    }

    public function test_supervisor_cannot_delete_asset()
    {
        $supervisor = $this->createSupervisor();
        $this->assertFalse($supervisor->can('delete', $this->asset));
    }

    public function test_technician_cannot_delete_asset()
    {
        $technician = $this->createTechnician();
        $this->assertFalse($technician->can('delete', $this->asset));
    }

    public function test_admin_and_supervisor_can_update_asset()
    {
        $admin = $this->createAdmin();
        $supervisor = $this->createSupervisor();
        
        $this->assertTrue($admin->can('update', $this->asset));
        $this->assertTrue($supervisor->can('update', $this->asset));
    }

    public function test_technician_cannot_update_asset()
    {
        $technician = $this->createTechnician();
        $this->assertFalse($technician->can('update', $this->asset));
    }

    public function test_auditor_can_view_asset()
    {
        $auditor = $this->createAuditor();
        $this->assertTrue($auditor->can('view', $this->asset));
    }
}