<?php

namespace Tests\Feature\Dashboard;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\WorkOrder;
use App\Models\Fault;
use App\Models\MaintenanceSchedule;

class DashboardTest extends TestCase
{
    public function test_dashboard_shows_correct_stats_for_admin()
    {
        $admin = $this->createAdmin();
        
        Asset::factory()->count(5)->create();
        WorkOrder::factory()->count(3)->create(['status' => 'pending']);
        Fault::factory()->count(2)->create(['severity' => 'critical', 'status' => 'reported']);
        MaintenanceSchedule::factory()->count(4)->create(['is_active' => true]);

        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
    }

    public function test_dashboard_shows_pending_verification_for_supervisor()
    {
        $supervisor = $this->createSupervisor();
        
        WorkOrder::factory()->count(3)->create(['status' => 'completed']);

        $response = $this->actingAs($supervisor)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Pending Verification');
        $response->assertSee('3');
    }

    public function test_technician_sees_only_their_work_orders_on_dashboard()
    {
        $technician = $this->createTechnician();
        $otherTechnician = $this->createTechnician();
        
        WorkOrder::factory()->create([
            'technician_id' => $technician->id,
            'title' => 'My Work Order',
        ]);
        WorkOrder::factory()->create([
            'technician_id' => $otherTechnician->id,
            'title' => 'Not My Work Order',
        ]);

        $response = $this->actingAs($technician)->get('/');

        $response->assertSee('My Work Order');
        $response->assertDontSee('Not My Work Order');
    }
}