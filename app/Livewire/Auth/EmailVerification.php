<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Providers\RouteServiceProvider;

/**
 * Email Verification component for verifying user email addresses
 */
#[Layout('components.layouts.auth')]
class EmailVerification extends Component
{
    /**
     * Status message after verification attempt
     */
    public ?string $status = null;

    /**
     * Verification link sent status
     */
    public bool $verificationLinkSent = false;

    /**
     * Send a new email verification link
     */
    public function sendVerificationEmail(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirect(route('admin.dashboard'));
            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        $this->verificationLinkSent = true;
        session()->flash('resent', true);
    }

    /**
     * Render the email verification component
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.auth.email-verification');
    }
}
