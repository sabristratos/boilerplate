<?php

namespace App\Livewire\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Registration component for new user accounts
 */
#[Layout('components.auth-layout')]
class Register extends Component
{
    /**
     * User's name
     *
     * @var string
     */
    public string $name = '';

    /**
     * User's email address
     *
     * @var string
     */
    public string $email = '';

    /**
     * User's password
     *
     * @var string
     */
    public string $password = '';

    /**
     * Password confirmation
     *
     * @var string
     */
    public string $password_confirmation = '';

    /**
     * Validation rules for the registration form
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Custom validation messages
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => __('Please enter your name.'),
            'email.required' => __('Please enter your email address.'),
            'email.email' => __('Please enter a valid email address.'),
            'email.unique' => __('This email address is already in use.'),
            'password.required' => __('Please enter a password.'),
            'password.min' => __('Your password must be at least 8 characters.'),
            'password.confirmed' => __('The password confirmation does not match.'),
        ];
    }

    /**
     * Register a new user
     *
     * @return void
     */
    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('admin.dashboard'));
    }

    /**
     * Render the registration component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.auth.register');
    }
}
