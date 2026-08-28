<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('users')->exists()) {
            return;
        }

        $sql = file_get_contents(database_path('seeders/data/users.sql'));

        if ($sql === false) {
            throw new \RuntimeException('Kullanıcı seed verisi okunamadı.');
        }

        DB::unprepared($sql);
    }
}
