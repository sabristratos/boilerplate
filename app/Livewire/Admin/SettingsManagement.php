<?php

namespace App\Livewire\Admin;

use App\Enums\SettingType;
use App\Facades\Settings;
use App\Models\Setting;
use App\Models\SettingGroup;
use Flux\Flux;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.admin-layout')]
class SettingsManagement extends Component
{
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
            } else {
                $this->values[$setting->key] = $setting->value;
            }
        }
    }

    public function save()
    {
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

        foreach ($this->values as $key => $value) {
            if (isset($settingsCollection[$key])) {
                Settings::set($key, $value);
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
