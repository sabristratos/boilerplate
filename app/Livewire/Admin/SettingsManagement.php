<?php

namespace App\Livewire\Admin;

use App\Enums\SettingType;
use App\Facades\Settings;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Services\AttachmentService;
use Flux\Flux;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;

/**
 * Settings management component
 */
#[Layout('components.layouts.admin')]
class SettingsManagement extends Component
{
    use WithFileUploads;

    #[Url(keep: true)]
    public $tab = 'general';

    #[Url(keep: true)]
    public string $currentLocale;

    public $values = [];
    public array $locales;

    public function mount()
    {
        $this->locales = config('app.available_locales', ['en' => 'English']);
        $this->currentLocale = array_key_first($this->locales);
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = Setting::all();

        foreach ($settings as $setting) {
            $value = $setting->value;
            if ($setting->isTranslatableAttribute('value') && $this->currentLocale !== config('app.fallback_locale')) {
                 $value = $setting->getTranslation('value', $this->currentLocale, false) ?? $setting->value;
            }

            if ($setting->type === SettingType::CHECKBOX || $setting->type === SettingType::BOOLEAN) {
                $this->values[$setting->key] = (bool) $value;
            } elseif ($setting->type === SettingType::MULTISELECT) {
                $this->values[$setting->key] = json_decode($value, true) ?? [];
            } else {
                $this->values[$setting->key] = $value;
            }
        }
    }

    public function updatedCurrentLocale()
    {
        $this->loadSettings();
    }

    public function selectTab(string $tab)
    {
        $this->tab = $tab;
    }

    public function save()
    {
        $this->authorize('update', Setting::class);
        $this->resetErrorBag();

        $settingsCollection = Setting::all()->keyBy('key');
        $validationRules = [];
        $validationAttributes = [];
        $validationMessages = [];

        foreach ($settingsCollection as $key => $setting) {
            $rule = [];
            if ($setting->is_required) {
                $rule[] = 'required';
            }

            if ($setting->validation_rules) {
                $rule = array_merge($rule, explode('|', $setting->validation_rules));
            }

            if (!empty($rule)) {
                $validationRules['values.' . $key] = implode('|', $rule);
            }
            $validationAttributes['values.' . $key] = $setting->getTranslation('display_name', $this->currentLocale) ?? $setting->display_name;
        }

        $validator = Validator::make(
            ['values' => $this->values],
            $validationRules,
            [],
            $validationAttributes
        );

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());
            Flux::toast(
                text: __('Please fix the errors before saving.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        foreach ($this->values as $key => $currentValue) {
            if (!isset($settingsCollection[$key])) {
                continue;
            }

            $setting = $settingsCollection[$key];
            $originalValue = $setting->value;
            $newValue = $currentValue;

            if ($currentValue instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                try {
                    $settingFile = \App\Models\SettingFile::create();
                    $attachment = app(AttachmentService::class)->upload($currentValue, $settingFile, $key);
                    $newValue = $attachment->id;
                } catch (\Exception $e) {
                    Log::error('Failed to upload setting file: ' . $e->getMessage());
                    Flux::toast(
                        text: __('Failed to upload file for :name setting.', ['name' => $setting->getTranslation('display_name', $this->currentLocale)]),
                        heading: __('Error'),
                        variant: 'danger'
                    );
                    continue;
                }
            }

            $isDirty = false;
            if ($setting->type === SettingType::CHECKBOX || $setting->type === SettingType::BOOLEAN) {
                if ((bool) $originalValue !== (bool) $newValue) {
                    $isDirty = true;
                }
            } elseif ($setting->type === SettingType::MULTISELECT) {
                // Filter out any false values from the array, which may be sent from unchecked checkboxes.
                $newValue = (array) $currentValue;
                if (json_decode($originalValue, true) !== $newValue) {
                    $isDirty = true;
                    $newValue = json_encode($newValue);
                }
            } elseif ((string) $originalValue !== (string) $newValue) {
                $isDirty = true;
            }

            if ($isDirty) {
                if ($setting->isTranslatableAttribute('value') && $this->currentLocale !== config('app.fallback_locale')) {
                    $setting->setTranslation('value', $this->currentLocale, $newValue);
                } else {
                    $setting->value = $newValue;
                }
                $setting->save();
            }
        }

        $this->loadSettings();

        Flux::toast(
            text: __('You can always update this in your settings.'),
            heading: __('Changes saved.'),
            variant: 'success'
        );

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.admin.settings-management', [
            'groups' => Settings::allGroups(),
        ]);
    }
}
