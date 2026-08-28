<?php

namespace Database\Factories;

use App\Models\model_exercises;
use App\Models\model_questions;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\model_questions>
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
        return [
            'question' => fake()->sentence(rand(3, 7)),
            'image' => fake()->boolean(60) ? fake()->numberBetween(0, 9) . '.jpg' : null,
            'exercise_id' => model_exercises::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (model_questions $question): void {
            $optionTexts = fake()->boolean(20)
                ? ['True', 'False']
                : collect(range(1, fake()->numberBetween(2, 5)))
                    ->map(fn (): string => fake()->sentence(fake()->numberBetween(1, 3)))
                    ->all();
            $correctPosition = fake()->numberBetween(1, count($optionTexts));

            $question->options()->createMany(
                collect($optionTexts)
                    ->values()
                    ->map(fn (string $optionText, int $index): array => [
                        'option_text' => $optionText,
                        'display_position' => $index + 1,
                        'is_correct' => ($index + 1) === $correctPosition,
                    ])
                    ->all(),
            );
        });
    }
}
