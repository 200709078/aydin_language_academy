<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\answers_model>
 */
class results_modelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=>rand(2,10),
            'exam_id'=>rand(1,10),
            'point'=>rand(1,100),
            'correct_number'=>rand(1,20),
            'wrong_number'=>rand(1,10)
        ];
    }
}
