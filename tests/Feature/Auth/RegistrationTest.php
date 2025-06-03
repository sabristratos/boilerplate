<?php

namespace Tests\Feature\Auth;

use App\Livewire\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registration_page_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    /** @test */
    public function users_can_register()
    {
        Event::fake();

        Livewire::test(\App\Livewire\Livewire\Auth\Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertRedirect('/dashboard');

        $this->assertTrue(User::whereEmail('test@example.com')->exists());
        $this->assertAuthenticated();
        Event::assertDispatched(Registered::class);
    }

    /** @test */
    public function name_is_required()
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function email_is_required()
    {
        Livewire::test(\App\Livewire\Livewire\Auth\Register::class)
            ->set('name', 'Test User')
            ->set('email', '')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function email_must_be_valid()
    {
        Livewire::test(\App\Livewire\Livewire\Auth\Register::class)
            ->set('name', 'Test User')
            ->set('email', 'not-an-email')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function email_must_be_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        Livewire::test(\App\Livewire\Livewire\Auth\Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    public function password_is_required()
    {
        Livewire::test(\App\Livewire\Livewire\Auth\Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('register')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        Livewire::test(\App\Livewire\Livewire\Auth\Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'different-password')
            ->call('register')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    /** @test */
    public function password_must_be_minimum_8_characters()
    {
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('register')
            ->assertHasErrors(['password' => 'min']);
    }
}
