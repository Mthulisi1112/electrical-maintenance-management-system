<?php

namespace Tests\Feature\WorkOrderManagement;

use Tests\TestCase;
use App\Models\MaintenanceSchedule;
use App\Models\Asset;
use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderGenerationTest extends TestCase
{
    public function test_supervisor_can_generate_work_order_from_schedule()
    {
        $supervisor = $this->createSupervisor();
        $technician = $this->createTechnician();
        $asset = Asset::factory()->create();
        
        $schedule = MaintenanceSchedule::factory()->create([
            'asset_id' => $asset->id,
            'is_active' => true,
            'next_due_date' => now()->addDays(7),
        ]);

        $response = $this->actingAs($supervisor)
            ->post("/maintenance-schedules/{$schedule->id}/generate-work-order");

        $response->assertStatus(302);
        $this->assertDatabaseHas('work_orders', [
            'maintenance_schedule_id' => $schedule->id,
            'asset_id' => $asset->id,
            'title' => 'Scheduled Maintenance: ' . $schedule->title,
            'type' => 'preventive',
        ]);
    }

    public function test_cannot_generate_work_order_without_technician()
    {
        $supervisor = $this->createSupervisor();
        $asset = Asset::factory()->create();
        
        // Delete all technicians
        User::whereHas('role', fn($q) => $q->where('slug', 'technician'))->delete();
        
        $schedule = MaintenanceSchedule::factory()->create([
            'asset_id' => $asset->id,
        ]);

        $response = $this->actingAs($supervisor)
            ->post("/maintenance-schedules/{$schedule->id}/generate-work-order");

        $response->assertSessionHas('error');
    }

    public function test_generated_work_order_has_correct_details()
    {
        $supervisor = $this->createSupervisor();
        $technician = $this->createTechnician();
        $asset = Asset::factory()->create();
        
        $schedule = MaintenanceSchedule::factory()->create([
            'asset_id' => $asset->id,
            'title' => 'Test Schedule',
            'description' => 'Test Description',
            'checklist_items' => ['Item 1', 'Item 2'],
            'next_due_date' => now()->addDays(7),
        ]);

        $this->actingAs($supervisor)
            ->post("/maintenance-schedules/{$schedule->id}/generate-work-order");

        $workOrder = WorkOrder::where('maintenance_schedule_id', $schedule->id)->first();
        
        $this->assertEquals('Scheduled Maintenance: Test Schedule', $workOrder->title);
        $this->assertEquals('Test Description', $workOrder->description);
        $this->assertEquals(['Item 1', 'Item 2'], $workOrder->checklist);
        $this->assertEquals('preventive', $workOrder->type);
        $this->assertEquals('pending', $workOrder->status);
    }
}