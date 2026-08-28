<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubLevelSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('sub_levels')->exists()) {
            return;
        }

        $sql = file_get_contents(database_path('seeders/data/sub_levels.sql'));

        if ($sql === false) {
            throw new \RuntimeException('Tema alt seviye seed verisi okunamadı.');
        }

        DB::unprepared($sql);
    }
}
