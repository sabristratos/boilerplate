<?php

namespace Database\Factories;

use App\Models\PageView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PageViewFactory extends Factory
{
    protected $model = PageView::class;

    public function definition(): array
    {
        return [
            'session_id' => $this->faker->uuid(),
            'path' => $this->faker->word(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->word(),
            'referrer' => $this->faker->word(),
            'utm_source' => $this->faker->word(),
            'utm_medium' => $this->faker->word(),
            'utm_campaign' => $this->faker->word(),
            'utm_term' => $this->faker->word(),
            'utm_content' => $this->faker->word(),
            'device_type' => $this->faker->word(),
            'browser_name' => $this->faker->name(),
            'platform_name' => $this->faker->name(),
            'visited_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
