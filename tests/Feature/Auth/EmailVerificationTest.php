<?php

namespace Tests\Feature\Auth;

use App\Livewire\Livewire\Auth\EmailVerification;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function email_verification_screen_can_be_rendered()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
    }

    /** @test */
    public function email_can_be_verified()
    {
        Event::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect('/dashboard?verified=1');
    }

    /** @test */
    public function email_is_not_verified_with_invalid_hash()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    /** @test */
    public function user_can_resend_verification_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Livewire::actingAs($user)
            ->test(EmailVerification::class)
            ->call('sendVerificationEmail')
            ->assertSet('verificationLinkSent', true);
    }

    /** @test */
    public function verified_users_are_redirected_from_verification_page()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Livewire\Auth\EmailVerification::class)
            ->call('sendVerificationEmail')
            ->assertRedirect('/dashboard');
    }
}
