<?php

namespace Tests\Feature\MaintenanceSchedule;

use Tests\TestCase;
use App\Models\MaintenanceSchedule;
use App\Models\Asset;

class ScheduleCrudTest extends TestCase
{
    public function test_supervisor_can_create_maintenance_schedule()
    {
        $supervisor = $this->createSupervisor();
        $asset = Asset::factory()->create();

        $response = $this->actingAs($supervisor)->post('/maintenance-schedules', [
            'asset_id' => $asset->id,
            'title' => 'Monthly Inspection',
            'description' => 'Inspect all components',
            'frequency' => 'monthly',
            'priority' => 'medium',
            'start_date' => now()->format('Y-m-d'),
            'estimated_duration_minutes' => 120,
            'checklist_items' => ['Check oil', 'Inspect belts', 'Test operation'],
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('maintenance_schedules', [
            'asset_id' => $asset->id,
            'title' => 'Monthly Inspection',
            'created_by' => $supervisor->id,
        ]);
    }

    public function test_technician_cannot_create_schedule()
    {
        $technician = $this->createTechnician();
        $asset = Asset::factory()->create();

        $response = $this->actingAs($technician)->post('/maintenance-schedules', [
            'asset_id' => $asset->id,
            'title' => 'Monthly Inspection',
            'description' => 'Inspect all components',
            'frequency' => 'monthly',
            'priority' => 'medium',
            'start_date' => now()->format('Y-m-d'),
            'estimated_duration_minutes' => 120,
        ]);

        $response->assertStatus(403);
    }

    public function test_supervisor_can_toggle_schedule_active_status()
    {
        $supervisor = $this->createSupervisor();
        $schedule = MaintenanceSchedule::factory()->create(['is_active' => true]);

        $response = $this->actingAs($supervisor)
            ->patch("/maintenance-schedules/{$schedule->id}/toggle-active");

        $response->assertStatus(302);
        $schedule->refresh();
        $this->assertFalse($schedule->is_active);
    }

    public function test_next_due_date_updates_after_completion()
    {
        $schedule = MaintenanceSchedule::factory()->create([
            'frequency' => 'monthly',
            'last_completed_date' => null,
            'next_due_date' => now()->addDays(30),
        ]);

        $schedule->updateNextDueDate();
        
        $this->assertNotNull($schedule->last_completed_date);
        $this->assertEquals(
            now()->addMonth()->format('Y-m-d'),
            $schedule->next_due_date->format('Y-m-d')
        );
    }
}