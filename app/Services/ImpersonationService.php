<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Session;

class ImpersonationService
{
    /**
     * The session key used to store the original user's ID.
     */
    protected const IMPERSONATION_KEY = 'impersonation_original_user_id';

    /**
     * Check if a user is currently being impersonated.
     */
    public function isImpersonating(): bool
    {
        return Session::has(self::IMPERSONATION_KEY);
    }

    /**
     * Get the ID of the user who is doing the impersonating.
     */
    public function getImpersonatorId(): ?int
    {
        return Session::get(self::IMPERSONATION_KEY);
    }

    /**
     * Impersonate a given user.
     *
     * @param \App\Models\User $impersonator The user who is performing the impersonation.
     * @param \App\Models\User $impersonated The user to be impersonated.
     */
    public function impersonate(User $impersonator, User $impersonated): bool
    {
        if ($this->isImpersonating()) {
            return false; // Already impersonating
        }

        Session::put(self::IMPERSONATION_KEY, $impersonator->id);
        auth()->login($impersonated);

        return true;
    }

    /**
     * Leave the current impersonation and revert to the original user.
     */
    public function leave(): bool
    {
        if (!$this->isImpersonating()) {
            return false;
        }

        $originalUserId = $this->getImpersonatorId();
        $originalUser = User::find($originalUserId);

        if ($originalUser) {
            auth()->login($originalUser);
            Session::forget(self::IMPERSONATION_KEY);

            return true;
        }

        return false;
    }
} 