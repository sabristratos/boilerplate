<?php

namespace Database\Factories;

use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Term>
 */
class TermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word;

        return [
            'taxonomy_id' => Taxonomy::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence,
            'parent_id' => null,
            'order' => 0,
        ];
    }
}
