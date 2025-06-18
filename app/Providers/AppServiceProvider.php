<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->tuneModelBehavior();
        $this->enforceSecureUrls();
        $this->optimizeViteSettings();
        $this->overrideLanguageSettings();
    }

    /**
     * Fine-tune Eloquent model behavior.
     */
    private function tuneModelBehavior(): void
    {
        Model::shouldBeStrict();
    }
    /**
     * Force HTTPS in non-local environments.
     */
    private function enforceSecureUrls(): void
    {
        if (!$this->app->environment('local')) {
            URL::forceScheme('https');
        }
    }
    /**
     * Optimize Vite asset loading strategy.
     */
    private function optimizeViteSettings(): void
    {
        Vite::usePrefetchStrategy('aggressive');
    }

    /**
     * Override language settings from database.
     */
    private function overrideLanguageSettings(): void
    {
        try {
            if (Schema::hasTable('settings')) {
                $availableLanguagesSetting = DB::table('settings')->where('key', 'available_languages')->value('value');
                if ($availableLanguagesSetting) {
                    $languageCodes = json_decode((string) $availableLanguagesSetting, true);
                    if (is_array($languageCodes)) {
                        $languageMap = [
                            'en' => 'English',
                            'fr' => 'Français',
                            'es' => 'Español',
                        ];
                        $availableLanguages = array_intersect_key($languageMap, array_flip($languageCodes));
                        config(['app.available_locales' => $availableLanguages]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Could not load language settings from database: ' . $e->getMessage());
        }
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }
}
