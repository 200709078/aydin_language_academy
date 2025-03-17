<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\exams_model>
 */
class exams_modelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(rand(3, 7));
        $statuses = ['publish', 'unpublish', 'draft'];
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->text(200),
            'status' => $statuses[rand(0, 2)]
        ];
    }
}
