<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('levels')->exists()) {
            return;
        }

        $sql = file_get_contents(database_path('seeders/data/levels.sql'));

        if ($sql === false) {
            throw new \RuntimeException('Tema seviye seed verisi okunamadı.');
        }

        DB::unprepared($sql);
    }
}
