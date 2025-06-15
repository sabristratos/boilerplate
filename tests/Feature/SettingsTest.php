<?php

namespace Tests\Feature;

use App\Livewire\Admin\SettingsManagement;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Indicates if the test database should be refreshed using transactions.
     *
     * @var bool
     */
    protected $refreshDatabaseWithTransactions = false;

    #[Test]
    public function settings_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $this->actingAs($user);

        $this->get(route('admin.settings'))
            ->assertStatus(200);
    }

    #[Test]
    public function can_update_a_setting_with_valid_data()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $this->actingAs($user);

        $setting = Setting::where('key', 'site_name')->first();
        $newValue = 'New Site Name';

        Livewire::test(SettingsManagement::class)
            ->set('values.' . $setting->key, $newValue)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('settings', [
            'key' => $setting->key,
            'value' => json_encode(['en' => $newValue], JSON_UNESCAPED_UNICODE),
        ]);
    }

    #[Test]
    public function cannot_update_a_setting_with_invalid_data()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $this->actingAs($user);

        $setting = Setting::where('key', 'site_name')->first();
        // This is invalid because site_name is required.
        $newValue = '';

        Livewire::test(SettingsManagement::class)
            ->set('values.' . $setting->key, $newValue)
            ->call('save')
            ->assertHasErrors(['values.' . $setting->key => 'required']);
    }

    #[Test]
    public function validation_rules_from_database_are_applied()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');
        $this->actingAs($user);

        $setting = Setting::where('key', 'mail_from_address')->first();
        $newValue = 'not-an-email';

        Livewire::test(SettingsManagement::class)
            ->set('values.' . $setting->key, $newValue)
            ->call('save')
            ->assertHasErrors(['values.' . $setting->key => 'email']);
    }
} 