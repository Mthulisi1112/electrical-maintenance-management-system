<?php

namespace Tests\Feature\RoleBasedAccess;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\WorkOrder;
use App\Models\Fault;
use App\Models\MaintenanceSchedule;
use App\Models\User;

class AdminAccessTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = $this->createAdmin();
    }

    public function test_admin_can_access_all_modules()
    {
        $routes = [
            '/assets',
            '/assets/create',
            '/work-orders',
            '/work-orders/create',
            '/faults',
            '/faults/create',
            '/maintenance-schedules',
            '/maintenance-schedules/create',
            '/reports',
            '/admin/users',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_admin_can_create_edit_delete_all_entities()
    {
        $asset = Asset::factory()->create();
        $workOrder = WorkOrder::factory()->create();
        $fault = Fault::factory()->create();
        $schedule = MaintenanceSchedule::factory()->create();

        // Test edit access
        $this->assertTrue($this->admin->can('update', $asset));
        $this->assertTrue($this->admin->can('update', $workOrder));
        $this->assertTrue($this->admin->can('update', $fault));
        $this->assertTrue($this->admin->can('update', $schedule));

        // Test delete access
        $this->assertTrue($this->admin->can('delete', $asset));
        $this->assertTrue($this->admin->can('delete', $workOrder));
        $this->assertTrue($this->admin->can('delete', $fault));
        $this->assertTrue($this->admin->can('delete', $schedule));
    }

    public function test_admin_can_manage_users()
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);

        $user = User::factory()->create();
        $response = $this->actingAs($this->admin)->get("/admin/users/{$user->id}/edit");
        $response->assertStatus(200);
    }
}