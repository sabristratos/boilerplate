<?php

namespace Database\Factories;

use App\Models\SettingGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingGroupFactory extends Factory
{
    protected $model = SettingGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->sentence,
            'order' => $this->faker->randomDigitNotNull,
        ];
    }
} 