<?php

namespace Tests\Feature\Search;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\WorkOrder;
use App\Models\User;
use App\Models\MaintenanceSchedule;

class SearchFunctionalityTest extends TestCase
{
    public function test_search_returns_matching_assets()
    {
        $user = $this->createTechnician();
        $asset = Asset::factory()->create(['name' => 'Unique Motor Name']);

        $response = $this->actingAs($user)->get('/search?q=Unique');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $asset->name,
            'type' => 'asset',
        ]);
    }

    public function test_search_returns_matching_work_orders()
    {
        $user = $this->createTechnician();
        $workOrder = WorkOrder::factory()->create(['title' => 'Emergency Repair']);

        $response = $this->actingAs($user)->get('/search?q=Emergency');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $workOrder->title,
            'type' => 'work-order',
        ]);
    }

    public function test_search_returns_no_results_for_short_query()
    {
        $user = $this->createTechnician();

        $response = $this->actingAs($user)->get('/search?q=a');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_technician_cannot_search_users()
    {
        $technician = $this->createTechnician();
        $user = User::factory()->create(['name' => 'Secret User']);

        $response = $this->actingAs($technician)->get('/search?q=Secret');

        $response->assertStatus(200);
        $response->assertJsonMissing([
            'type' => 'user',
        ]);
    }

    public function test_admin_can_search_users()
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create(['name' => 'Secret User']);

        $response = $this->actingAs($admin)->get('/search?q=Secret');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => $user->name,
            'type' => 'user',
        ]);
    }
}