<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $dropCheck = DB::connection()->isMaria() ? 'DROP CONSTRAINT' : 'DROP CHECK';
        DB::statement("ALTER TABLE scholarship_applications
            ADD COLUMN correct_count SMALLINT UNSIGNED NULL AFTER score,
            ADD COLUMN wrong_count SMALLINT UNSIGNED NULL AFTER correct_count,
            ADD COLUMN blank_count SMALLINT UNSIGNED NULL AFTER wrong_count,
            {$dropCheck} sch_app_result_complete_ck,
            ADD CONSTRAINT sch_app_result_award_ck CHECK (result_published = 0 OR scholarship_percentage IS NOT NULL),
            ADD CONSTRAINT sch_app_absent_counts_ck CHECK (attendance_status <> 'absent' OR (correct_count IS NULL AND wrong_count IS NULL AND blank_count IS NULL))");
    }

    public function down(): void
    {
        // Restoring the old rule must not silently change already published results.
        if (DB::table('scholarship_applications')->where('result_published', true)->whereNull('score')->exists()) {
            throw new RuntimeException('Unpublish results without a score before rolling back this migration.');
        }
        $dropCheck = DB::connection()->isMaria() ? 'DROP CONSTRAINT' : 'DROP CHECK';
        DB::statement("ALTER TABLE scholarship_applications
            {$dropCheck} sch_app_absent_counts_ck,
            {$dropCheck} sch_app_result_award_ck,
            ADD CONSTRAINT sch_app_result_complete_ck CHECK (result_published = 0 OR (score IS NOT NULL AND scholarship_percentage IS NOT NULL)),
            DROP COLUMN correct_count,
            DROP COLUMN wrong_count,
            DROP COLUMN blank_count");
    }
};
