<?php

namespace App\Livewire\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Forgot Password component for requesting password reset links
 */
#[Layout('components.auth-layout')]
class ForgotPassword extends Component
{
    /**
     * User's email address
     *
     * @var string
     */
    public string $email = '';

    /**
     * Status message after form submission
     *
     * @var string|null
     */
    public ?string $status = null;

    /**
     * Validation rules for the forgot password form
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'email' => 'required|email',
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
        ];
    }

    /**
     * Send a password reset link to the user
     *
     * @return void
     */
    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(
            ['email' => $this->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            $this->status = __($status);
            $this->email = '';
        } else {
            $this->addError('email', __($status));
        }
    }

    /**
     * Render the forgot password component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
