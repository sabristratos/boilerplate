<?php

namespace App\Http\Middleware;

use App\Facades\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorChallengeIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $requireTwoFactor = Settings::get('require_two_factor_auth', false);

        if ($user) {
            // If 2FA is required globally and user doesn't have it enabled
            if ($requireTwoFactor && !$user->hasTwoFactorEnabled() &&
                !$request->is('user/two-factor-authentication') &&
                !$request->is('logout')) {
                return redirect()->route('two-factor.setup')
                    ->with('warning', 'Two-factor authentication is required for all users.');
            }

            // If user has 2FA enabled but hasn't completed the challenge
            if ($user->hasTwoFactorEnabled() &&
                !$request->session()->has('auth.two_factor.confirmed') &&
                !$request->is('two-factor-challenge') &&
                !$request->is('logout')
            ) {
                // Mark that 2FA is required
                $request->session()->put('auth.two_factor.required', true);

                return redirect()->route('two-factor.challenge');
            }
        }

        return $next($request);
    }
}
