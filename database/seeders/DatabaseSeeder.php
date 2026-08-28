<?php

namespace Database\Seeders;

use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_themes;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LevelSeeder::class,
            SubLevelSeeder::class,
        ]);

        model_themes::factory(50)->create();
        model_exercises::factory(100)->create();
        model_declarations::factory(100)->create();
        $this->call(ExerciseQuestionDemoSeeder::class);
    }
}
