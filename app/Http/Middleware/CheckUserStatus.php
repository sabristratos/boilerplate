<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status !== UserStatus::Active) {
            $status = Auth::user()->status;
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = match ($status) {
                UserStatus::Inactive => __('Your account is inactive.'),
                default => __('Your account has been suspended or is inactive.'),
            };

            return redirect('/')->with('error', $message);
        }

        return $next($request);
    }
}
