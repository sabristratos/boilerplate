<?php

namespace App\Livewire\Auth;

use App\Facades\ActivityLogger;
use App\Facades\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Login component for user authentication
 */
#[Layout('components.layouts.auth')]
class Login extends Component
{
    /**
     * User's email address
     */
    public string $email = '';

    /**
     * User's password
     */
    public string $password = '';

    /**
     * Remember me checkbox state
     */
    public bool $remember = false;

    /**
     * Validation rules for the login form
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
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
            'email.required' => __('Please enter your email address.'),
            'email.email' => __('Please enter a valid email address.'),
            'password.required' => __('Please enter your password.'),
        ];
    }

    /**
     * Attempt to authenticate the user
     */
    public function login(): void
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            // Log successful login
            ActivityLogger::logCustom(
                'login',
                'User logged in',
                Auth::user(),
                Auth::user(),
                [
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'remember' => $this->remember,
                ],
                'auth'
            );

            $this->redirect(route('admin.dashboard'));
        } else {
            // Check if failed login attempts should be logged
            if (Settings::get('log_failed_login_attempts', true)) {
                // Standard logging
                Log::warning('Failed login attempt', [
                    'email' => $this->email,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                // Activity logging
                ActivityLogger::logCustom(
                    'login_failed',
                    'Failed login attempt',
                    null,
                    null,
                    [
                        'email' => $this->email,
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ],
                    'auth'
                );
            }

            $this->addError('email', __('These credentials do not match our records.'));
        }
    }

    /**
     * Render the login component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.auth.login');
    }
}
