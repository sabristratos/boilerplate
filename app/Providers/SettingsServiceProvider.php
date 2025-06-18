<?php

namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsService::class, fn($app) => new SettingsService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only run if the application is ready (database is connected)
        if ($this->app->isBooted() && !$this->app->runningInConsole()) {
            try {
                $settings = app(SettingsService::class);

                // Override app config
                config(['app.name' => $settings->get('site_name', config('app.name'))]);
                config(['app.locale' => $settings->get('default_language', config('app.locale'))]);

                // Override mail config
                config(['mail.default' => $settings->get('mail_mailer', config('mail.default'))]);
                config(['mail.mailers.smtp.host' => $settings->get('mail_host', config('mail.mailers.smtp.host'))]);
                config(['mail.mailers.smtp.port' => $settings->get('mail_port', config('mail.mailers.smtp.port'))]);
                config(['mail.mailers.smtp.username' => $settings->get('mail_username', config('mail.mailers.smtp.username'))]);
                config(['mail.mailers.smtp.password' => $settings->get('mail_password', config('mail.mailers.smtp.password'))]);
                config(['mail.mailers.smtp.encryption' => $settings->get('mail_encryption', config('mail.mailers.smtp.encryption'))]);
                config(['mail.from.address' => $settings->get('mail_from_address', config('mail.from.address'))]);
                config(['mail.from.name' => $settings->get('mail_from_name', config('mail.from.name'))]);

                // Override session config for timeout
                config(['session.lifetime' => $settings->get('session_timeout', config('session.lifetime'))]);
            } catch (\Exception $e) {
                // If there's an error (like during installation when tables don't exist yet), just continue
                report($e);
            }
        }
    }
}
