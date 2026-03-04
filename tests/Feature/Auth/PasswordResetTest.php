<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class PasswordResetTest extends TestCase
{
    public function test_reset_password_link_screen_can_be_rendered()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested()
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status'); // Check for status message

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered()
    {
        // Create a user and generate a token manually
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->get('/reset-password/' . $token);

        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'test@reset.com',
            'password' => Hash::make('old-password'),
        ]);

        // Generate a token
        $token = Password::createToken($user);

        // Attempt to reset password
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@reset.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('new-password123', $user->password));
    }
}