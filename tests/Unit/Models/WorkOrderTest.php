<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\WorkOrder;
use App\Models\Asset;
use App\Models\User;

class WorkOrderTest extends TestCase
{
    public function test_work_order_belongs_to_asset()
    {
        $workOrder = WorkOrder::factory()->create();
        $this->assertInstanceOf(Asset::class, $workOrder->asset);
    }

    public function test_work_order_belongs_to_technician()
    {
        $workOrder = WorkOrder::factory()->create();
        $this->assertInstanceOf(User::class, $workOrder->technician);
    }

    public function test_work_order_belongs_to_supervisor()
    {
        $workOrder = WorkOrder::factory()->create();
        $this->assertInstanceOf(User::class, $workOrder->supervisor);
    }

    public function test_work_order_casts_dates_correctly()
    {
        $workOrder = WorkOrder::factory()->create([
            'scheduled_date' => '2025-03-01',
            'started_at' => '2025-03-01 10:00:00',
            'completed_date' => '2025-03-01 14:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $workOrder->scheduled_date);
        $this->assertInstanceOf(\Carbon\Carbon::class, $workOrder->started_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $workOrder->completed_date);
    }

    public function test_work_order_casts_json_fields()
    {
        $checklist = ['Task 1', 'Task 2'];
        $partsUsed = [['name' => 'Part A', 'quantity' => 2]];

        $workOrder = WorkOrder::factory()->create([
            'checklist' => $checklist,
            'parts_used' => $partsUsed,
        ]);

        $this->assertIsArray($workOrder->checklist);
        $this->assertEquals($checklist, $workOrder->checklist);
        $this->assertEquals($partsUsed, $workOrder->parts_used);
    }
}