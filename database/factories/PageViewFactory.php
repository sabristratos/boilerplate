<?php

namespace Database\Factories;

use App\Models\PageView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageView>
 */
class PageViewFactory extends Factory
{
    protected $model = PageView::class;

    public function definition(): array
    {
        return [
            'session_id' => fake()->uuid(),
            'path' => fake()->word(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->word(),
            'referrer' => fake()->word(),
            'utm_source' => fake()->word(),
            'utm_medium' => fake()->word(),
            'utm_campaign' => fake()->word(),
            'utm_term' => fake()->word(),
            'utm_content' => fake()->word(),
            'device_type' => fake()->word(),
            'browser_name' => fake()->name(),
            'platform_name' => fake()->name(),
            'visited_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
