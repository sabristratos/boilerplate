<?php

use App\Http\Controllers\ImpersonationController;
use App\Livewire\Admin\ActivityLogManagement;
use App\Livewire\Admin\AnalyticsDashboard;
use App\Livewire\Admin\AttachmentManagement;
use App\Livewire\Admin\ManageAttachment;
use App\Livewire\Admin\Settings\Index as SettingsIndex;
use App\Livewire\Admin\Taxonomies\Index as TaxonomyIndex;
use App\Livewire\Admin\Taxonomies\ManageTaxonomy;
use App\Livewire\Admin\Terms\Index as TermIndex;
use App\Livewire\Admin\Terms\ManageTerm;
use App\Livewire\Admin\UploadAttachments;
use App\Livewire\Admin\UserProfile;
use App\Livewire\Admin\NotificationManagement;
use App\Livewire\Admin\Translations\ManageTranslations;
use App\Livewire\Admin\Crud\Index as CrudIndex;
use App\Livewire\Admin\Crud\Form as CrudForm;
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
Route::get('/settings', SettingsIndex::class)
    ->name('settings')
    ->middleware('can:viewAny,App\Models\Setting');

// Translations
Route::get('/translations', ManageTranslations::class)->name('translations.index');

// Attachment Management
Route::get('/attachments', AttachmentManagement::class)
    ->name('attachments')
    ->middleware('can:viewAny,App\Models\Attachment');
Route::get('/attachments/{attachment}/edit', ManageAttachment::class)
    ->name('attachments.edit')
    ->middleware('can:update,attachment');

// Activity Log Management
Route::get('/activity-logs', ActivityLogManagement::class)
    ->name('activity-logs')
    ->middleware('can:viewAny,App\Models\ActivityLog');

// Notification Management
Route::middleware(['can:viewAny,App\Models\Notification'])->group(function () {
    Route::get('/notifications', NotificationManagement::class)->name('notifications');
});


// Generic CRUD Routes
Route::prefix('crud/{alias}')->name('crud.')->group(function () {
    Route::get('/', CrudIndex::class)->name('index');
    Route::get('/create', CrudForm::class)->name('create');
    Route::get('/{id}/edit', CrudForm::class)->name('edit');
});

// Add more admin routes here as needed 