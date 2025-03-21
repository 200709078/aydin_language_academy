<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\questions_model>
 */
class model_questionsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create();
        return [
            'question' => fake()->sentence(rand(3, 7)),
            'image' => fake()->imageUrl(800, 400, 'cats', true, true),
            'answer1' => fake()->sentence(rand(1, 3)),
            'answer2' => fake()->sentence(rand(1, 3)),
            'answer3' => fake()->sentence(rand(1, 3)),
            'answer4' => fake()->sentence(rand(1, 3)),
            'answer5' => fake()->sentence(rand(1, 3)),
            'correct_answer' => 'answer' . rand(1, 5),
            'exercises_id' => rand(1, 100)
        ];
    }
}
