<?php

use App\Actions\Auth\Logout;
use App\Livewire\Auth\TwoFactorChallenge;
use App\Livewire\Auth\EmailVerification;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

// Two-factor authentication challenge
Route::get('/two-factor-challenge', TwoFactorChallenge::class)
    ->middleware(['auth'])
    ->name('two-factor.challenge');

// Email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', EmailVerification::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (Request $request) {
        $user = User::findOrFail($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1((string) $user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route('admin.dashboard').'?verified=1');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});

// Logout route
Route::post('/logout', Logout::class)->middleware('auth')->name('logout'); 