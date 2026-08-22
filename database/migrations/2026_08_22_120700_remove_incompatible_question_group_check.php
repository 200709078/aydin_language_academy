<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove the MariaDB-only group-shape check added before MySQL compatibility
     * was reviewed. The equivalent invariant is enforced by the question model.
     */
    public function up(): void
    {
        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'placement_test_questions')
            ->where('CONSTRAINT_NAME', 'pt_q_group_shape_ck')
            ->exists();

        if (! $exists) {
            return;
        }

        $version = strtolower((string) DB::selectOne('select version() as version')->version);
        $dropKeyword = str_contains($version, 'mariadb') ? 'CONSTRAINT' : 'CHECK';

        DB::statement("ALTER TABLE `placement_test_questions` DROP {$dropKeyword} `pt_q_group_shape_ck`");
    }

    /**
     * The portable model invariant intentionally remains in place on rollback.
     */
    public function down(): void {}
};
