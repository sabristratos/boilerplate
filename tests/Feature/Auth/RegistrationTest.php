<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register');

        $this->assertTrue(User::whereEmail('test@example.com')->exists());
        $this->assertAuthenticated();
    }

    /** @test */
    public function name_is_required()
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->call('register')
            ->assertHasErrors(['name' => 'required']);
    }

    /** @test */
    public function email_is_required()
    {
        Livewire::test(Register::class)
            ->set('email', '')
            ->call('register')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function email_must_be_valid()
    {
        Livewire::test(Register::class)
            ->set('email', 'not-an-email')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function email_must_be_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);

        Livewire::test(Register::class)
            ->set('email', 'test@example.com')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    /** @test */
    public function password_is_required()
    {
        Livewire::test(Register::class)
            ->set('password', '')
            ->call('register')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        Livewire::test(Register::class)
            ->set('password', 'password')
            ->set('password_confirmation', 'not-password')
            ->call('register')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    /** @test */
    public function password_must_be_minimum_8_characters()
    {
        Livewire::test(Register::class)
            ->set('password', 'secret')
            ->set('password_confirmation', 'secret')
            ->call('register')
            ->assertHasErrors(['password' => 'min']);
    }
}
