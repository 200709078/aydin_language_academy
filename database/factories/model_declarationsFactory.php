<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nette\Utils\Random;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\model_declarations>
 */
class model_declarationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->text(50);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'contents' => fake()->text(200),
            'image' => rand(0, 9).'.jpg',
            'pdf' => 'deneme.pdf',
            'video' => 'https://youtu.be/gAHzzHD-jVA',
            'voice' => 'https://youtu.be/gAHzzHD-jVA',
            'answerkey' => 'answers.pdf',
            'theme_id' => rand(1,50)
        ];
    }
}
