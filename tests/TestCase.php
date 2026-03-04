<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Set mail to array driver for testing
        config(['mail.default' => 'array']);
    }

    protected function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->role_id = Role::where('slug', 'admin')->first()->id;
        $admin->save();
        return $admin;
    }

    protected function createSupervisor(): User
    {
        $supervisor = User::factory()->create();
        $supervisor->role_id = Role::where('slug', 'maintenance-supervisor')->first()->id;
        $supervisor->save();
        return $supervisor;
    }

    protected function createTechnician(): User
    {
        $technician = User::factory()->create();
        $technician->role_id = Role::where('slug', 'technician')->first()->id;
        $technician->save();
        return $technician;
    }

    protected function createAuditor(): User
    {
        $auditor = User::factory()->create();
        $auditor->role_id = Role::where('slug', 'auditor')->first()->id;
        $auditor->save();
        return $auditor;
    }
}