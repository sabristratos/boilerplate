<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class TwoFactorAuthentication extends Component
{
    /**
     * Indicates if 2FA QR code is being displayed.
     */
    public bool $showingQrCode = false;

    /**
     * Indicates if 2FA recovery codes are being displayed.
     */
    public bool $showingRecoveryCodes = false;

    /**
     * The OTP code for confirming 2FA.
     */
    public string $code = '';

    /**
     * Confirmation message.
     */
    public ?string $message = null;

    /**
     * Error message.
     */
    public ?string $error = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        // Reset state when component is mounted
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;
        $this->code = '';
        $this->message = null;
        $this->error = null;
    }

    /**
     * Enable two factor authentication for the user.
     */
    public function enableTwoFactorAuthentication(): void
    {
        $user = Auth::user();

        // If 2FA is already enabled, don't do anything
        if ($user->hasTwoFactorEnabled()) {
            return;
        }

        // Enable 2FA for the user
        $user->enableTwoFactorAuthentication();

        // Show the QR code
        $this->showingQrCode = true;
        $this->showingRecoveryCodes = false;
        $this->message = null;
        $this->error = null;
    }

    /**
     * Confirm two factor authentication for the user.
     */
    public function confirmTwoFactorAuthentication(): void
    {
        $user = Auth::user();

        // If 2FA is already confirmed, don't do anything
        if ($user->hasConfirmedTwoFactor()) {
            return;
        }

        // Validate the code
        if (! $user->validateTwoFactorCode($this->code)) {
            $this->error = 'The provided two factor authentication code was invalid.';
            return;
        }

        // Confirm 2FA for the user
        $user->confirmTwoFactorAuthentication();

        // Show the recovery codes
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = true;
        $this->message = 'Two factor authentication has been enabled.';
        $this->error = null;
    }

    /**
     * Display the user's recovery codes.
     */
    public function showRecoveryCodes(): void
    {
        $user = Auth::user();

        // If 2FA is not enabled, don't do anything
        if (! $user->hasTwoFactorEnabled()) {
            return;
        }

        // Show the recovery codes
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = true;
        $this->message = null;
        $this->error = null;
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(): void
    {
        $user = Auth::user();

        // If 2FA is not enabled, don't do anything
        if (! $user->hasTwoFactorEnabled()) {
            return;
        }

        // Generate new recovery codes
        $twoFactorService = app(\App\Services\TwoFactorAuthenticationService::class);
        $user->two_factor_recovery_codes = $twoFactorService->generateRecoveryCodes();
        $user->save();

        // Show the recovery codes
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = true;
        $this->message = 'Recovery codes have been regenerated.';
        $this->error = null;
    }

    /**
     * Disable two factor authentication for the user.
     */
    public function disableTwoFactorAuthentication(): void
    {
        $user = Auth::user();

        // If 2FA is not enabled, don't do anything
        if (! $user->hasTwoFactorEnabled()) {
            return;
        }

        // Disable 2FA for the user
        $user->disableTwoFactorAuthentication();

        // Reset the component state
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;
        $this->message = 'Two factor authentication has been disabled.';
        $this->error = null;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.profile.two-factor-authentication', [
            'user' => Auth::user(),
        ]);
    }
}
