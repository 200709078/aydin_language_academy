<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nette\Utils\Random;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\model_exercises>
 */
class model_exercisesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->text(50);
        return [
            'title' => $name,
            'slug' => Str::slug($name),
/*             'image' => rand(0, 9).'.jpg',
            'voice'=>'https://youtu.be/mGOPSuKx8uU',
            'video'=>'https://youtu.be/mGOPSuKx8uU',
            'qtext' => fake()->text(600), */
            'theme_id' => rand(1,50)
        ];
    }
}
