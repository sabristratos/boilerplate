<?php

namespace App\Actions\Auth;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Action class for handling user logout
 */
class Logout
{
    /**
     * Create a new action instance.
     *
     * @return void
     */
    public function __construct(
        /**
         * The guard implementation.
         */
        protected StatefulGuard $guard
    )
    {
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        $this->guard->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get the guard instance.
     */
    public function getGuard(): StatefulGuard
    {
        return $this->guard;
    }
}
