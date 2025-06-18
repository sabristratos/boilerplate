<?php

namespace Database\Factories;

use App\Enums\SettingType;
use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'setting_group_id' => SettingGroup::factory(),
            'key' => $this->faker->unique()->word,
            'display_name' => $this->faker->sentence,
            'value' => $this->faker->word,
            'type' => SettingType::TEXT,
            'is_public' => $this->faker->boolean,
            'is_required' => false,
            'order' => $this->faker->randomDigitNotNull,
        ];
    }
} 