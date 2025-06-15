<?php

namespace Database\Factories;

use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Taxonomy>
 */
class TaxonomyFactory extends Factory
{
    protected $model = Taxonomy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
            'name' => [
                'en' => $name,
                'fr' => $name . ' (FR)'
            ],
            'description' => [
                'en' => $this->faker->sentence(),
                'fr' => $this->faker->sentence() . ' (FR)'
            ],
            'slug' => Str::slug($name),
        ];
    }
}
