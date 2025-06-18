<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class TwoFactorChallenge extends Component
{
    /**
     * The code for two-factor authentication.
     */
    public string $code = '';

    /**
     * The recovery code for two-factor authentication.
     */
    public string $recoveryCode = '';

    /**
     * Indicates if recovery codes are being used.
     */
    public bool $usingRecoveryCode = false;

    /**
     * Error message.
     */
    public ?string $error = null;

    /**
     * Toggle between code and recovery code inputs.
     */
    public function toggleRecoveryCode(): void
    {
        $this->usingRecoveryCode = !$this->usingRecoveryCode;
        $this->code = '';
        $this->recoveryCode = '';
        $this->error = null;
    }

    /**
     * Verify the two-factor authentication code.
     */
    public function verifyCode(): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->redirect('/login');
            return;
        }

        if ($this->usingRecoveryCode) {
            if ($user->validateTwoFactorRecoveryCode($this->recoveryCode)) {
                session()->forget('auth.two_factor.required');
                $this->redirect('/dashboard');
                return;
            }

            $this->error = 'The provided recovery code is invalid.';
            return;
        }

        if ($user->validateTwoFactorCode($this->code)) {
            session()->forget('auth.two_factor.required');
            $this->redirect('/dashboard');
            return;
        }

        $this->error = 'The provided two-factor authentication code is invalid.';
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.auth.two-factor-challenge');
    }
}
