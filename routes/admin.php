<?php

use App\Http\Controllers\ImpersonationController;
use App\Livewire\Admin\ActivityLogManagement;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Livewire\Admin\AttachmentManagement;
use App\Livewire\Admin\Legal\EditLegalPage;
use App\Livewire\Admin\Legal\LegalPageManagement;
use App\Livewire\Admin\Roles\Index as RoleIndex;
use App\Livewire\Admin\Roles\ManageRole;
use App\Livewire\Admin\SettingsManagement;
use App\Livewire\Admin\Taxonomies\Index as TaxonomyIndex;
use App\Livewire\Admin\Taxonomies\ManageTaxonomy;
use App\Livewire\Admin\Terms\Index as TermIndex;
use App\Livewire\Admin\Terms\ManageTerm;
use App\Livewire\Admin\Users\Index as UserIndex;
use App\Livewire\Admin\Users\ImportUsers;
use App\Livewire\Admin\Users\ManageUser;
use App\Livewire\Admin\UserProfile;
use App\Livewire\Admin\NotificationManagement;
use App\Livewire\Admin\Translations\ManageTranslations;
use Illuminate\Support\Facades\Route;

// Impersonation routes
Route::get('/users/{user}/impersonate', [ImpersonationController::class, 'start'])
    ->name('users.impersonate')
    ->middleware('can:impersonate,user');

Route::get('/users/impersonate/stop', [ImpersonationController::class, 'stop'])
    ->name('users.impersonate.stop');

Route::get('/profile', UserProfile::class)->name('profile');

Route::middleware(['can:viewAny,App\Models\User'])->group(function () {
    Route::get('/', AnalyticsDashboard::class)->name('dashboard');
});

// User Management
Route::middleware(['can:viewAny,App\Models\User'])->group(function () {
    Route::get('/users', UserIndex::class)->name('users.index');
    Route::get('/users/import', ImportUsers::class)->name('users.import')->middleware('can:create,App\Models\User');
    Route::get('/users/create', ManageUser::class)->name('users.create')->middleware('can:create,App\Models\User');
    Route::get('/users/{user}/edit', ManageUser::class)->name('users.edit')->middleware('can:update,user');
});

// Role Management
Route::middleware(['can:viewAny,App\Models\Role'])->group(function () {
    Route::get('/roles', RoleIndex::class)->name('roles.index');
    Route::get('/roles/create', ManageRole::class)->name('roles.create')->middleware('can:create,App\Models\Role');
    Route::get('/roles/{role}/edit', ManageRole::class)->name('roles.edit')->middleware('can:update,role');
});

// Taxonomy Management
Route::middleware(['can:viewAny,App\Models\Taxonomy'])->group(function () {
    Route::get('/taxonomies', TaxonomyIndex::class)->name('taxonomies.index');
    Route::get('/taxonomies/create', ManageTaxonomy::class)->name('taxonomies.create')->middleware('can:create,App\Models\Taxonomy');
    Route::get('/taxonomies/{taxonomy}/edit', ManageTaxonomy::class)->name('taxonomies.edit')->middleware('can:update,taxonomy');
    Route::middleware(['can:viewAny,App\Models\Term'])->group(function () {
        Route::get('/taxonomies/{taxonomy}/terms', TermIndex::class)->name('taxonomies.terms.index');
        Route::get('/taxonomies/{taxonomy}/terms/create', ManageTerm::class)->name('taxonomies.terms.create')->middleware('can:create,App\Models\Term');
        Route::get('/taxonomies/{taxonomy}/terms/{term}/edit', ManageTerm::class)->name('taxonomies.terms.edit')->middleware('can:update,term');
    });
});

// Settings Management
Route::get('/settings', SettingsManagement::class)
    ->name('settings')
    ->middleware('can:viewAny,App\Models\Setting');

// Translations
Route::get('/translations', ManageTranslations::class)->name('translations.index');

// Attachment Management
Route::get('/attachments', AttachmentManagement::class)
    ->name('attachments')
    ->middleware('can:viewAny,App\Models\Attachment');

// Activity Log Management
Route::get('/activity-logs', ActivityLogManagement::class)
    ->name('activity-logs')
    ->middleware('can:viewAny,App\Models\ActivityLog');

// Notification Management
Route::middleware(['can:viewAny,App\Models\Notification'])->group(function () {
    Route::get('/notifications', NotificationManagement::class)->name('notifications');
});

// Legal Page Management
Route::middleware(['can:viewAny,App\Models\LegalPage'])->group(function () {
    Route::get('/legal-pages', LegalPageManagement::class)->name('legal-pages.index');
    Route::get('/legal-pages/create', EditLegalPage::class)->name('legal-pages.create')->middleware('can:create,App\Models\LegalPage');
    Route::get('/legal-pages/{legalPage}/edit', EditLegalPage::class)->name('legal-pages.edit')->middleware('can:update,legalPage');
});

// Add more admin routes here as needed 