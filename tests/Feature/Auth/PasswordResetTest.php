<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
        $user = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendResetLink');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
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
        $user = User::factory()->create();
        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, [
            'token' => $token,
            'email' => $user->email,
        ])
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('resetPassword');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function email_is_required_for_forgot_password()
    {
        Livewire::test(ForgotPassword::class)
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function email_must_be_valid_for_forgot_password()
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'not-a-valid-email')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function email_is_required_for_reset_password()
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->set('email', '')
            ->call('resetPassword')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function password_is_required_for_reset_password()
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->set('password', '')
            ->call('resetPassword')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function password_must_be_confirmed_for_reset_password()
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->set('password', 'password')
            ->set('password_confirmation', 'not-password')
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
