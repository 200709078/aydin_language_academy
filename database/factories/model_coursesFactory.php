<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nette\Utils\Random;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\model_courses>
 */
class model_coursesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title_tr' => fake()->text(20),
            'title_en' => fake()->text(20),
            'slogan_tr' => fake()->text(20),
            'slogan_en' => fake()->text(20),
            'description_tr' => fake()->sentence(rand(8, 28)),
            'description_en' => fake()->sentence(rand(8, 28)),
            'image' => rand(0, 9).'.jpg',
        ];
    }
}
