<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Fault;
use App\Models\Asset;
use App\Models\User;

class FaultTest extends TestCase
{
    public function test_fault_belongs_to_asset()
    {
        $fault = Fault::factory()->create();
        $this->assertInstanceOf(Asset::class, $fault->asset);
    }

    public function test_fault_belongs_to_reporter()
    {
        $fault = Fault::factory()->create();
        $this->assertInstanceOf(User::class, $fault->reportedBy);
    }

    public function test_fault_can_be_assigned_to_technician()
    {
        $technician = User::factory()->create();
        $fault = Fault::factory()->create();

        $fault->assignTo($technician);

        $this->assertEquals($technician->id, $fault->assigned_to);
        $this->assertEquals('investigating', $fault->status);
    }

    public function test_fault_can_be_resolved()
    {
        $fault = Fault::factory()->create();

        $fault->resolve('Worn bearing', 'Replaced bearing');

        $this->assertEquals('resolved', $fault->status);
        $this->assertEquals('Worn bearing', $fault->root_cause);
        $this->assertEquals('Replaced bearing', $fault->corrective_actions);
        $this->assertNotNull($fault->downtime_end);
    }

    public function test_downtime_minutes_calculated_automatically()
    {
        $start = now()->subHours(3);
        $end = now();

        $fault = Fault::factory()->create([
            'downtime_start' => $start,
            'downtime_end' => $end,
        ]);

        // Fix 1: Use assertEqualsWithDelta for precision tolerance
        $this->assertEqualsWithDelta(180, $fault->downtime_minutes, 1, 'Downtime minutes should be approximately 180');
        
        // OR Fix 2: Use a range check
        // $this->assertGreaterThanOrEqual(179, $fault->downtime_minutes);
        // $this->assertLessThanOrEqual(181, $fault->downtime_minutes);
        
        // OR Fix 3: Round the value
        // $this->assertEquals(180, round($fault->downtime_minutes));
    }

    public function test_is_resolved_method_works()
    {
        $resolvedFault = Fault::factory()->create(['status' => 'resolved']);
        $closedFault = Fault::factory()->create(['status' => 'closed']);
        $openFault = Fault::factory()->create(['status' => 'reported']);

        $this->assertTrue($resolvedFault->isResolved());
        $this->assertTrue($closedFault->isResolved());
        $this->assertFalse($openFault->isResolved());
    }
}