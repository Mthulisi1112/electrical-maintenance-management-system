<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class RegistrationTest extends TestCase
{
    public function test_new_users_get_default_technician_role()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        
        $user = User::where('email', 'test@example.com')->first();
        $technicianRole = Role::where('slug', 'technician')->first();
        
        $this->assertEquals($technicianRole->id, $user->role_id);
        $this->assertTrue($user->is_active);
        $this->assertNull($user->email_verified_at);
    }

    public function test_users_can_register_with_optional_fields()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'employee_id' => 'EMP-12345',
            'phone' => '+1234567890',
        ]);

        $response->assertStatus(302);
        
        $user = User::where('email', 'test@example.com')->first();
        $this->assertEquals('EMP-12345', $user->employee_id);
        $this->assertEquals('+1234567890', $user->phone);
    }

    public function test_duplicate_email_cannot_register()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}