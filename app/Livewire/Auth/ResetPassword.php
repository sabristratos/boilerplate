<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Reset Password component for resetting user passwords
 */
#[Layout('components.layouts.auth')]
class ResetPassword extends Component
{
    /**
     * The password reset token
     *
     * @var string
     */
    public string $token = '';

    /**
     * User's email address
     *
     * @var string
     */
    public string $email = '';

    /**
     * User's new password
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
     * Status message after form submission
     *
     * @var string|null
     */
    public ?string $status = null;

    /**
     * Mount the component with the token and email
     *
     * @param string $token
     * @param string|null $email
     * @return void
     */
    public function mount(string $token, ?string $email = null): void
    {
        $this->token = $token;
        $this->email = $email ?? '';
    }

    /**
     * Validation rules for the reset password form
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
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
            'password.required' => __('Please enter a new password.'),
            'password.min' => __('Your password must be at least 8 characters.'),
            'password.confirmed' => __('The password confirmation does not match.'),
        ];
    }

    /**
     * Reset the user's password
     *
     * @return void
     */
    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->status = __($status);
        } else {
            $this->addError('email', __($status));
        }
    }

    /**
     * Render the reset password component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.auth.reset-password');
    }
} 