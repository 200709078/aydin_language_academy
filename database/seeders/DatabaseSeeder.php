<?php

namespace Database\Seeders;
use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_questions;
use App\Models\User;
use App\Models\model_levels;
use App\Models\model_sub_levels;
use App\Models\model_themes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $levels = ['Grammar', 'Vocabulary', 'LISTENING'];
        foreach ($levels as $level) {
            model_levels::insert([
                'name' => $level,
                'slug' => Str::slug($level),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $sub_levels = ['Elemantary', 'Intermediate', 'Pre-Intermediate'];
        foreach ($sub_levels as $sub_level) {
            model_sub_levels::insert([
                'name' => $sub_level,
                'slug' => Str::slug($sub_level),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        User::insert([
            'name' => 'Adem VAROL',
            'email' => 'aaa@mail.com',
            'email_verified_at' => now(),
            'type' => 'admin',
            'password' => '$2y$12$Sj8XJ5vfQBpW6opvmlrgTuvnbOKWHBDvg.6o1ZTuKSJPuOzI7oEzu',
            'remember_token' => Str::random(10),
        ]);

        model_themes::factory(50)->create();
        model_exercises::factory(100)->create();
        model_questions::factory(1000)->create();

        for ($i = 1; $i < 11; $i++) {
            $title='DECLARATIONS_' . (string) $i;
            model_declarations::insert([
                'title' => $title,
                'slug' => Str::slug($title),
                'contents' => fake()->text(200),
                'theme_id' => (string)$i
            ]);
        }
    }
}
