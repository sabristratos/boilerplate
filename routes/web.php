<?php

use App\Http\Controllers\ImpersonationController;
use App\Livewire\Profile\TwoFactorAuthentication;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LegalPageController;

// Home route
Route::get('/', fn() => view('home'));


// Dashboard route (protected)
Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureTwoFactorChallengeIsComplete::class])->group(function () {

    // Stop impersonation route
    Route::get('/users/impersonate/stop', [ImpersonationController::class, 'stop'])->name('admin.users.impersonate.stop');

    // Notification Preferences
    Route::get('/user/notification-preferences', \App\Livewire\Profile\NotificationPreferences::class)->name('profile.notification-preferences');

    // Two-factor authentication setup
    Route::get('/user/two-factor-authentication', TwoFactorAuthentication::class)->name('two-factor.setup');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        require __DIR__.'/admin.php';
    });
});

require __DIR__.'/auth.php';

Route::get('/language/{locale}', function ($locale) {
    if (array_key_exists($locale, config('app.available_locales', []))) {
        app()->setLocale($locale);
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch');

Route::get('/legal/{slug}', [LegalPageController::class, 'show'])->name('legal.show');
