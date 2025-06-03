<?php

namespace App\Livewire\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Email Verification component for verifying user email addresses
 */
#[Layout('components.auth-layout')]
class EmailVerification extends Component
{
    /**
     * Status message after verification attempt
     *
     * @var string|null
     */
    public ?string $status = null;

    /**
     * Verification link sent status
     *
     * @var bool
     */
    public bool $verificationLinkSent = false;

    /**
     * Send a new email verification link
     *
     * @return void
     */
    public function sendVerificationEmail(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirect(route('dashboard'));
            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        $this->verificationLinkSent = true;
        session()->flash('resent', true);
    }

    /**
     * Render the email verification component
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        return view('livewire.auth.email-verification');
    }
}
