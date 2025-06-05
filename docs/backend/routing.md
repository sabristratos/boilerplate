---
## `routes/web.php`

This file defines all routes accessible via a web browser. It includes public pages, authentication-related routes, and protected user/admin dashboards.

### Key Route Groups & Features:

1.  **Public Routes:**
    *   `GET /`: Returns a `test` view. Likely a placeholder or simple landing page.
    *   `GET /css/dynamic.css`: Points to `App\Http\Controllers\DynamicCssController@index`. Used for serving dynamically generated CSS.

2.  **Authenticated User Routes:**
    *   These routes are grouped under `auth`, `verified`, and `App\Http\Middleware\EnsureTwoFactorChallengeIsComplete` middleware, ensuring users are logged in, have verified their email, and completed 2FA if enabled.
    *   `GET /dashboard`: Displays the main user dashboard view.
    *   `GET /user/two-factor-authentication`: Routes to the `App\Livewire\Profile\TwoFactorAuthentication` Livewire component for managing 2FA settings.

3.  **Admin Routes:**
    *   Prefixed with `/admin` and route names prefixed with `admin.`.
    *   Also protected by the same middleware as general authenticated routes.
    *   `GET /admin/`: Returns the `admin.dashboard` view.
    *   Several routes point to Livewire components for managing various aspects of the admin panel:
        *   `GET /admin/profile` -> `App\Livewire\Admin\UserProfile`
        *   `GET /admin/roles` -> `App\Livewire\Admin\RoleManagement`
        *   `GET /admin/users` -> `App\Livewire\Admin\UserManagement`
        *   `GET /admin/settings` -> `App\Livewire\Admin\SettingsManagement`
        *   `GET /admin/attachments` -> `App\Livewire\Admin\AttachmentManagement`
        *   `GET /admin/taxonomies` -> `App\Livewire\Admin\TaxonomyManagement`
        *   `GET /admin/activity-logs` -> `App\Livewire\Admin\ActivityLogManagement`
        *   `GET /admin/analytics` -> `App\Livewire\Admin\AnalyticsDashboard`

4.  **Guest Routes (Authentication):**
    *   Grouped under `guest` middleware, meaning these are only accessible to users who are not logged in.
    *   All point to Livewire components for authentication flows:
        *   `GET /login` -> `App\Livewire\Livewire\Auth\Login` (named `login`)
        *   `GET /register` -> `App\Livewire\Livewire\Auth\Register` (named `register`)
        *   `GET /forgot-password` -> `App\Livewire\Livewire\Auth\ForgotPassword` (named `password.request`)
        *   `GET /reset-password/{token}` -> `App\Livewire\Livewire\Auth\ResetPassword` (named `password.reset`)

5.  **Two-Factor Challenge:**
    *   `GET /two-factor-challenge`: Routes to `App\Livewire\Auth\TwoFactorChallenge` Livewire component. Protected by `auth` middleware. (named `two-factor.challenge`).

6.  **Email Verification Routes:**
    *   Protected by `auth` middleware.
    *   `GET /email/verify`: Shows a notice to verify email, handled by `App\Livewire\Livewire\Auth\EmailVerification` Livewire component (named `verification.notice`).
    *   `GET /email/verify/{id}/{hash}`: Handles the actual email verification link. It contains custom logic to find the user, verify the hash, mark the email as verified, and dispatch the `Illuminate\Auth\Events\Verified` event. Protected by `signed` and `throttle:6,1` middleware (named `verification.verify`).

7.  **Logout Route:**
    *   `POST /logout`: Handles user logout using the `App\Actions\Auth\Logout` action class. Protected by `auth` middleware (named `logout`).

### Observations:

*   **Heavy use of Livewire:** Many routes delegate directly to Livewire components, indicating a significant portion of the UI interactivity is handled by Livewire.
*   **Standard Laravel Authentication Flow:** Implements typical Laravel authentication features including registration, login, password reset, email verification, and two-factor authentication.
*   **Clear Separation:** Good separation between guest, general authenticated user, and admin-specific routes using middleware and route prefixes.
*   **Custom Action for Logout:** Uses a dedicated action class (`App\Actions\Auth\Logout`) for the logout process, promoting cleaner controller/route logic.

---
## `routes/api.php`

This file is for registering API-specific routes. These routes are stateless and are typically protected by the `api` middleware group, often using token-based authentication like Laravel Sanctum.

### Key Routes:

1.  **Authenticated User Endpoint:**
    *   `GET /api/user`: Protected by `auth:sanctum` middleware. Returns the currently authenticated user's data (`$request->user()`). This is a standard Laravel Sanctum route.

2.  **Settings API Endpoints:**
    *   Routes are grouped under the `/api/settings` prefix.
    *   `GET /api/settings/`: Routes to `App\Http\Controllers\Api\SettingsController@index`.
        *   Likely used to retrieve a list of all publicly available settings.
    *   `GET /api/settings/{key}`: Routes to `App\Http\Controllers\Api\SettingsController@show`.
        *   Likely used to retrieve a specific public setting by its unique `key`.

### Observations:

*   **Sanctum Authentication:** Uses Laravel Sanctum for API authentication, as indicated by the `auth:sanctum` middleware.
*   **Dedicated Settings Controller:** API access to settings is managed by `App\Http\Controllers\Api\SettingsController`, suggesting a clear separation of concerns for API logic related to settings.
*   **Focus on Public Data:** The settings API seems geared towards exposing public configuration details, which is a common use case.

---
## `routes/console.php`

This file is where you can register all of your custom Artisan console commands. Artisan is Laravel's command-line interface.

### Defined Commands:

1.  **`inspire`**
    *   `Artisan::command('inspire', function () { $this->comment(Inspiring::quote()); })->purpose('Display an inspiring quote');`
    *   This is a default Laravel command that, when run (`php artisan inspire`), outputs an inspiring quote to the console.
    *   It uses the `Illuminate\Foundation\Inspiring` class to generate the quote.

### Observations:

*   **Minimal Custom Commands:** Currently, only the default `inspire` command is registered here. As the application grows, custom commands for tasks like data processing, scheduling, or application setup might be added to this file. 