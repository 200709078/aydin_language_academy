<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restore the nullable/null-on-delete master-content history policy.
     * Same-level ownership remains enforced by the result-content model.
     */
    public function up(): void
    {
        $database = DB::getDatabaseName();
        $foreignKeyExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $database)
            ->where('TABLE_NAME', 'placement_test_level_result_contents')
            ->where('CONSTRAINT_NAME', 'pt_lrc_content_level_fk')
            ->exists();

        if (! $foreignKeyExists) {
            return;
        }

        $sourceIndexExists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', 'placement_test_level_result_contents')
            ->where('INDEX_NAME', 'pt_lrc_content_source_ix')
            ->exists();

        if (! $sourceIndexExists) {
            Schema::table('placement_test_level_result_contents', function (Blueprint $table): void {
                $table->index('placement_test_question_content_id', 'pt_lrc_content_source_ix');
            });
        }

        DB::statement('ALTER TABLE `placement_test_level_result_contents` DROP FOREIGN KEY `pt_lrc_content_level_fk`');
        DB::statement('ALTER TABLE `placement_test_level_result_contents` DROP INDEX `pt_lrc_content_level_ix`');
    }

    /**
     * This compatibility migration intentionally does not restore the removed FK.
     */
    public function down(): void {}
};
