<?php

use App\Actions\Auth\Logout;
use App\Livewire\Admin\ActivityLogManagement;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Livewire\Admin\AttachmentManagement;
use App\Livewire\Admin\RoleManagement;
use App\Livewire\Admin\SettingsManagement;
use App\Livewire\Admin\TaxonomyManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\UserProfile;
use App\Livewire\Auth\TwoFactorChallenge;
use App\Livewire\Livewire\Auth\EmailVerification;
use App\Livewire\Livewire\Auth\ForgotPassword;
use App\Livewire\Livewire\Auth\Login;
use App\Livewire\Livewire\Auth\Register;
use App\Livewire\Livewire\Auth\ResetPassword;
use App\Livewire\Profile\TwoFactorAuthentication;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Home route
Route::get('/', function () {
    return view('test');
});

// Dynamic CSS route
Route::get('/css/dynamic.css', [\App\Http\Controllers\DynamicCssController::class, 'index'])
    ->name('dynamic.css');

// Dashboard route (protected)
Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureTwoFactorChallengeIsComplete::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Two-factor authentication setup
    Route::get('/user/two-factor-authentication', TwoFactorAuthentication::class)->name('two-factor.setup');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', UserProfile::class)->name('profile');
        Route::get('/roles', RoleManagement::class)->name('roles');
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/settings', SettingsManagement::class)->name('settings');
        Route::get('/attachments', AttachmentManagement::class)->name('attachments');
        Route::get('/taxonomies', TaxonomyManagement::class)->name('taxonomies');
        Route::get('/activity-logs', ActivityLogManagement::class)->name('activity-logs');
        Route::get('/analytics', AnalyticsDashboard::class)->name('analytics');

        // Add more admin routes here as needed
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');
    });
});

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

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(route('dashboard').'?verified=1');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});

// Logout route
Route::post('/logout', Logout::class)->middleware('auth')->name('logout');

Route::get('/language/{locale}', function ($locale) {
    if (array_key_exists($locale, config('app.available_locales', []))) {
        app()->setLocale($locale);
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch');
