<?php

namespace Database\Factories;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Term>
 */
class TermFactory extends Factory
{
    protected $model = Term::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
            'taxonomy_id' => Taxonomy::factory(),
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
