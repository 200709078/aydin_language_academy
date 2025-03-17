<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\questions_model>
 */
class questions_modelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exams_model_id'=>rand(1,10),
            'question'=>fake()->sentence(rand(3,7)),
            'select1'=>fake()->sentence(rand(1,3)),
            'select2'=>fake()->sentence(rand(1,3)),
            'select3'=>fake()->sentence(rand(1,3)),
            'select4'=>fake()->sentence(rand(1,3)),
            'select5'=>fake()->sentence(rand(1,3)),
            'correct_answer'=>'select'.rand(1,5)
        ];
    }
}
