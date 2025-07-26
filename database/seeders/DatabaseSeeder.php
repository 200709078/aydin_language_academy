<?php

namespace Database\Seeders;
use App\Models\model_declarations;
use App\Models\model_exercises;
use App\Models\model_questions;
use App\Models\model_results;
use App\Models\model_user_answers;
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
        $levels = ['GRAMMAR', 'VOCABULARY', 'LISTENING'];
        foreach ($levels as $level) {
            model_levels::insert([
                'name' => $level,
                'slug' => Str::slug($level),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $sub_levels = ['ELEMENTARY', 'INTERMEDIATE', 'PRE-INTERMEDIATE'];
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
            'email' => 'adem@learnenglishwithala.com',
            'email_verified_at' => now(),
            'type' => 'admin',
            //'password' => '$2y$12$Sj8XJ5vfQBpW6opvmlrgTuvnbOKWHBDvg.6o1ZTuKSJPuOzI7oEzu',
            'password' => '$2y$12$pVyMOcbht5SoAL1D1Kp3E.eocQcfKXufYbvOcOgQZ7S2RmbzBeXau',
            'remember_token' => Str::random(10),
        ]);

        User::factory(9)->create();
        model_themes::factory(50)->create();
        model_exercises::factory(100)->create();
        model_declarations::factory(100)->create();
        model_questions::factory(1000)->create();
/*         model_results::factory(1000)->create();
        model_user_answers::factory(1000)->create(); */
    }
}
