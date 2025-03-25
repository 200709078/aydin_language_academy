<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;
use Nette\Utils\Random;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\model_themes>
 */
class model_themesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $name = fake()->text(200);
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'image' => rand(0, 9).'.jpg',
            'level_id' => rand(1,3),
            'sub_level_id' => rand(1,3)
        ];
    }
}
