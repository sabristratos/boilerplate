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

/**
 * Settings management component
 */
#[Layout('components.layouts.admin')]
class SettingsManagement extends Component
{
    use WithFileUploads;

    #[Url]
    public $tab = 'general';

    public $values = [];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $settings = Setting::all();

        foreach ($settings as $setting) {
            if ($setting->type === SettingType::CHECKBOX || $setting->type === SettingType::BOOLEAN) {
                $this->values[$setting->key] = (bool) $setting->value;
            } elseif ($setting->type === SettingType::MULTISELECT) {
                $this->values[$setting->key] = json_decode($setting->value, true) ?? [];
            } else {
                $this->values[$setting->key] = $setting->value;
            }
        }
    }

    public function save()
    {
        Gate::authorize('edit-settings');
        $this->resetErrorBag();

        $settingsCollection = Setting::all()->keyBy('key');
        $validationRules = [];
        $validationAttributes = [];

        foreach ($settingsCollection as $key => $setting) {
            $rule = [];
            if ($setting->is_required) {
                $rule[] = 'required';
            }

            switch ($setting->type) {
                case SettingType::EMAIL:
                    $rule[] = 'email';
                    break;
                case SettingType::URL:
                    $rule[] = 'url';
                    break;
                case SettingType::NUMBER:
                    $rule[] = 'numeric';
                    break;
                case SettingType::FILE:
                    if (isset($this->values[$key]) && $this->values[$key] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        $rule[] = 'image';
                    }
                    break;
            }
            if (!empty($rule)) {
                $validationRules['values.' . $key] = implode('|', $rule);
            }
            $validationAttributes['values.' . $key] = $setting->display_name;
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
                $settingFile = \App\Models\SettingFile::create();
                $attachment = app(AttachmentService::class)->upload($currentValue, $settingFile, $key);
                $newValue = $attachment->id;
            }

            $isDirty = false;
            if ($setting->type === SettingType::CHECKBOX || $setting->type === SettingType::BOOLEAN) {
                if ((bool) $originalValue !== (bool) $newValue) {
                    $isDirty = true;
                }
            } elseif ($setting->type === SettingType::MULTISELECT) {
                // Filter out any false values from the array, which may be sent from unchecked checkboxes.
                $newValue = array_filter((array) $currentValue);
                if (json_decode($originalValue, true) !== $newValue) {
                    $isDirty = true;
                    $newValue = json_encode(array_values($newValue));
                }
            } elseif ((string) $originalValue !== (string) $newValue) {
                $isDirty = true;
            }

            if ($isDirty) {
                Settings::set($key, $newValue);
            }
        }

        $this->loadSettings();

        Flux::toast(
            text: 'You can always update this in your settings.',
            heading: 'Changes saved.',
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
