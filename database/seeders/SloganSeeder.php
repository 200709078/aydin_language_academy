<?php

namespace Database\Seeders;

use App\Models\Slogan;
use Illuminate\Database\Seeder;

class SloganSeeder extends Seeder
{
    public function run(): void
    {
        if (Slogan::query()->exists()) {
            return;
        }

        Slogan::query()->insert([
            [
                'title_tr' => 'Dil öğren, dünyanı genişlet.',
                'title_en' => 'Master language, empower success.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title_tr' => 'Sadece dil öğrenme. Onu yaşamaya başla.',
                'title_en' => "Don't just learn a language, start living it.",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}