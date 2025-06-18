<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected SettingsService $settingsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingsService = app(SettingsService::class);
        $this->seed('SettingsSeeder');
        $this->actingAs(User::factory()->create());
    }

    public function test_it_can_get_all_settings()
    {
        $settings = $this->settingsService->all();
        $this->assertNotEmpty($settings);
        $this->assertTrue(Cache::has(SettingsService::CACHE_KEY));
    }

    public function test_it_can_get_a_setting_by_key()
    {
        $siteName = $this->settingsService->get('site_name');
        $this->assertEquals('My Awesome Site', $siteName);
    }

    public function test_it_returns_default_value_for_non_existent_key()
    {
        $defaultValue = $this->settingsService->get('non_existent_key', 'default');
        $this->assertEquals('default', $defaultValue);
    }

    public function test_it_can_set_a_setting_value()
    {
        $this->settingsService->set('site_name', 'My New Site Name');
        $this->assertDatabaseHas('settings', ['key' => 'site_name', 'value' => 'My New Site Name']);
        $this->assertFalse(Cache::has(SettingsService::CACHE_KEY));
    }

    public function test_it_clears_cache_when_setting_is_updated()
    {
        // Prime the cache
        $this->settingsService->all();
        $this->assertTrue(Cache::has(SettingsService::CACHE_KEY));

        // Update a setting
        $this->settingsService->set('site_name', 'Another Name');

        // Assert cache is cleared
        $this->assertFalse(Cache::has(SettingsService::CACHE_KEY));
    }

    public function test_it_correctly_handles_boolean_setting()
    {
        $this->settingsService->set('show_logo_in_header', '0');
        $this->assertFalse($this->settingsService->get('show_logo_in_header'));

        $this->settingsService->set('show_logo_in_header', '1');
        $this->assertTrue($this->settingsService->get('show_logo_in_header'));
    }

    public function test_it_correctly_handles_number_setting()
    {
        Setting::factory()->create([
            'key' => 'items_per_page',
            'value' => '25',
            'type' => \App\Enums\SettingType::NUMBER,
        ]);

        $this->assertIsInt($this->settingsService->get('items_per_page'));
        $this->assertEquals(25, $this->settingsService->get('items_per_page'));
    }

    public function test_it_correctly_handles_json_setting()
    {
        $value = ['foo' => 'bar'];
        Setting::factory()->create([
            'key' => 'json_setting',
            'value' => json_encode($value),
            'type' => \App\Enums\SettingType::JSON,
        ]);

        $this->assertIsArray($this->settingsService->get('json_setting'));
        $this->assertEquals($value, $this->settingsService->get('json_setting'));
    }

    public function test_it_correctly_handles_file_setting()
    {
        $attachment = \App\Models\Attachment::factory()->create();
        Setting::factory()->create([
            'key' => 'site_logo',
            'value' => $attachment->id,
            'type' => \App\Enums\SettingType::FILE,
        ]);

        $url = $this->settingsService->get('site_logo');
        $this->assertStringContainsString($attachment->url, $url);
    }

    public function test_it_can_get_settings_by_group()
    {
        $generalSettings = $this->settingsService->group('general');
        $this->assertNotEmpty($generalSettings);
        $this->assertTrue($generalSettings->every(fn ($setting) => $setting->group->slug === 'general'));
    }

    public function test_it_can_get_public_settings()
    {
        $publicSettings = $this->settingsService->public();
        $this->assertNotEmpty($publicSettings);
        $this->assertTrue($publicSettings->every(fn ($setting) => $setting->is_public));
    }

    public function test_it_can_get_all_setting_groups_with_settings()
    {
        $settingGroups = $this->settingsService->allGroups();
        $this->assertNotEmpty($settingGroups);
        $this->assertTrue($settingGroups->first()->relationLoaded('settings'));
    }
} 