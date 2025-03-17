<?php

namespace Database\Seeders;
use App\Models\exams_model;
use App\Models\questions_model;
use App\Models\answers_model;
use App\Models\results_model;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::insert([
            'name' => 'Adem VAROL',
            'email' => 'aaa@mail.com',
            'email_verified_at' => now(),
            'type' => 'admin',
            'password' => '$2y$12$Sj8XJ5vfQBpW6opvmlrgTuvnbOKWHBDvg.6o1ZTuKSJPuOzI7oEzu',
            'remember_token' => Str::random(10),
        ]);
        User::factory(10)->create();
        exams_model::factory(10)->create();
        questions_model::factory(100)->create();
        answers_model::factory(1000)->create();
        results_model::factory(20)->create();
    }
}
