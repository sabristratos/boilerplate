<?php

namespace Tests\Feature\Auth;

use App\Livewire\Livewire\Auth\ForgotPassword;
use App\Livewire\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function forgot_password_page_can_be_rendered()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    /** @test */
    public function reset_password_link_can_be_requested()
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendResetLink');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /** @test */
    public function reset_password_page_can_be_rendered()
    {
        $user = User::factory()->create();

        $this->get('/reset-password/token?email=' . $user->email)
            ->assertStatus(200);
    }

    /** @test */
    public function password_can_be_reset_with_valid_token()
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendResetLink');

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $token = $notification->token;

            Livewire::test(\App\Livewire\Livewire\Auth\ResetPassword::class, ['token' => $token])
                ->set('email', $user->email)
                ->set('password', 'new-password')
                ->set('password_confirmation', 'new-password')
                ->call('resetPassword');

            return true;
        });

        $this->assertTrue(
            auth()->attempt([
                'email' => $user->email,
                'password' => 'new-password',
            ])
        );
    }

    /** @test */
    public function email_is_required_for_forgot_password()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', '')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function email_must_be_valid_for_forgot_password()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'not-an-email')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function email_is_required_for_reset_password()
    {
        Livewire::test(ResetPassword::class, ['token' => 'token'])
            ->set('email', '')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('resetPassword')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function password_is_required_for_reset_password()
    {
        Livewire::test(ResetPassword::class, ['token' => 'token'])
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('resetPassword')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function password_must_be_confirmed_for_reset_password()
    {
        Livewire::test(\App\Livewire\Livewire\Auth\ResetPassword::class, ['token' => 'token'])
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'different-password')
            ->call('resetPassword')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    /** @test */
    public function password_must_be_minimum_8_characters_for_reset_password()
    {
        Livewire::test(ResetPassword::class, ['token' => 'token'])
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('resetPassword')
            ->assertHasErrors(['password' => 'min']);
    }
}
