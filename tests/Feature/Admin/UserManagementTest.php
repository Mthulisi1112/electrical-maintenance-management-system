<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    // Create roles if they don't exist
    if (!Role::where('slug', 'admin')->exists()) {
        Role::create(['name' => 'Administrator', 'slug' => 'admin']);
    }
    if (!Role::where('slug', 'technician')->exists()) {
        Role::create(['name' => 'Technician', 'slug' => 'technician']);
    }
});

test('non-admin cannot access user management', function () {
    $user = User::factory()->create();
    $technicianRole = Role::where('slug', 'technician')->first();
    $user->role_id = $technicianRole->id;
    $user->save();

    $response = $this->actingAs($user)->get('/admin/users');
    $response->assertStatus(403);
});

test('admin can view user management page', function () {
    $admin = User::factory()->create();
    $adminRole = Role::where('slug', 'admin')->first();
    $admin->role_id = $adminRole->id;
    $admin->save();

    $response = $this->actingAs($admin)->get('/admin/users');
    $response->assertStatus(200);
    $response->assertSee('User Management');
});

test('admin can delete another user', function () {
    $admin = User::factory()->create();
    $adminRole = Role::where('slug', 'admin')->first();
    $admin->role_id = $adminRole->id;
    $admin->save();

    $userToDelete = User::factory()->create();

    $response = $this->actingAs($admin)
        ->delete("/admin/users/{$userToDelete->id}");

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertNull($userToDelete->fresh());
});

test('admin cannot delete themselves', function () {
    $admin = User::factory()->create();
    $adminRole = Role::where('slug', 'admin')->first();
    $admin->role_id = $adminRole->id;
    $admin->save();

    $response = $this->actingAs($admin)
        ->delete("/admin/users/{$admin->id}");

    $response->assertRedirect();
    $response->assertSessionHas('error');
    $this->assertNotNull($admin->fresh());
});

test('admin can toggle user active status', function () {
    $admin = User::factory()->create();
    $adminRole = Role::where('slug', 'admin')->first();
    $admin->role_id = $adminRole->id;
    $admin->save();

    $user = User::factory()->create(['is_active' => true]);

    $response = $this->actingAs($admin)
        ->patch("/admin/users/{$user->id}/toggle-status");

    $response->assertRedirect();
    $user->refresh();
    $this->assertFalse($user->is_active);
});

test('admin can edit user', function () {
    $admin = User::factory()->create();
    $adminRole = Role::where('slug', 'admin')->first();
    $admin->role_id = $adminRole->id;
    $admin->save();

    $user = User::factory()->create();
    $technicianRole = Role::where('slug', 'technician')->first();

    $response = $this->actingAs($admin)
        ->put("/admin/users/{$user->id}", [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role_id' => $technicianRole->id,
            'is_active' => true,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    
    $user->refresh();
    $this->assertEquals('Updated Name', $user->name);
    $this->assertEquals('updated@example.com', $user->email);
});