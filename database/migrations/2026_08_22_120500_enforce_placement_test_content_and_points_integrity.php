<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->assertScoringDataCanBeHardened();

        Schema::table('placement_test_question_contents', function (Blueprint $table): void {
            $table->unique(
                ['id', 'placement_test_level_id'],
                'pt_qc_id_level_uq',
            );
        });

        Schema::table('placement_test_questions', function (Blueprint $table): void {
            $table->decimal('points', 8, 2)->nullable(false)->change();
            $table->index(
                ['placement_test_question_content_id', 'placement_test_level_id'],
                'pt_q_content_level_ix',
            );
        });

        Schema::table('placement_test_questions', function (Blueprint $table): void {
            $table->foreign(
                ['placement_test_question_content_id', 'placement_test_level_id'],
                'pt_q_content_level_fk',
            )
                ->references(['id', 'placement_test_level_id'])
                ->on('placement_test_question_contents')
                ->restrictOnDelete();
        });

        Schema::table('placement_test_level_results', function (Blueprint $table): void {
            $table->decimal('total_points_snapshot', 13, 2)->nullable(false)->change();
            $table->decimal('correct_points', 13, 2)->nullable(false)->change();
        });

        Schema::table('placement_test_level_questions', function (Blueprint $table): void {
            $table->decimal('points_snapshot', 8, 2)->nullable(false)->change();
        });

        $this->addChecks();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropChecks();

        Schema::table('placement_test_level_questions', function (Blueprint $table): void {
            $table->decimal('points_snapshot', 8, 2)->nullable()->change();
        });

        Schema::table('placement_test_level_results', function (Blueprint $table): void {
            $table->decimal('total_points_snapshot', 13, 2)->nullable()->change();
            $table->decimal('correct_points', 13, 2)->nullable()->change();
        });

        Schema::table('placement_test_questions', function (Blueprint $table): void {
            $table->dropForeign('pt_q_content_level_fk');
            $table->dropIndex('pt_q_content_level_ix');
            $table->decimal('points', 8, 2)->nullable()->change();
        });

        Schema::table('placement_test_question_contents', function (Blueprint $table): void {
            $table->dropUnique('pt_qc_id_level_uq');
        });
    }

    private function addChecks(): void
    {
        DB::statement(<<<'SQL'
            ALTER TABLE `placement_test_question_contents`
            ADD CONSTRAINT `pt_qc_type_ck`
            CHECK (`type` IN ('text', 'audio', 'image', 'video'))
        SQL);
        DB::statement(<<<'SQL'
            ALTER TABLE `placement_test_question_contents`
            ADD CONSTRAINT `pt_qc_payload_ck`
            CHECK (
                (
                    `type` = 'text'
                    AND `text_content` IS NOT NULL
                    AND CHAR_LENGTH(TRIM(`text_content`)) > 0
                    AND `media_disk` IS NULL
                    AND `media_path` IS NULL
                )
                OR
                (
                    `type` IN ('audio', 'image', 'video')
                    AND `text_content` IS NULL
                    AND `media_disk` IS NOT NULL
                    AND CHAR_LENGTH(TRIM(`media_disk`)) > 0
                    AND `media_path` IS NOT NULL
                    AND CHAR_LENGTH(TRIM(`media_path`)) > 0
                    AND LOWER(TRIM(`media_path`)) NOT LIKE '%://%'
                    AND TRIM(`media_path`) NOT LIKE '//%'
                )
            )
        SQL);
        DB::statement(<<<'SQL'
            ALTER TABLE `placement_test_questions`
            ADD CONSTRAINT `pt_q_points_ck`
            CHECK (`points` > 0)
        SQL);
        DB::statement(<<<'SQL'
            ALTER TABLE `placement_test_level_results`
            ADD CONSTRAINT `pt_lr_total_points_ck`
            CHECK (`total_points_snapshot` > 0)
        SQL);
        DB::statement(<<<'SQL'
            ALTER TABLE `placement_test_level_results`
            ADD CONSTRAINT `pt_lr_correct_points_ck`
            CHECK (
                `correct_points` >= 0
                AND `correct_points` <= `total_points_snapshot`
            )
        SQL);
        DB::statement(<<<'SQL'
            ALTER TABLE `placement_test_level_questions`
            ADD CONSTRAINT `pt_lq_points_ck`
            CHECK (`points_snapshot` > 0)
        SQL);
        DB::statement(<<<'SQL'
            ALTER TABLE `placement_test_level_result_contents`
            ADD CONSTRAINT `pt_lrc_type_ck`
            CHECK (`type_snapshot` IN ('text', 'audio', 'image', 'video'))
        SQL);
        DB::statement(<<<'SQL'
            ALTER TABLE `placement_test_level_result_contents`
            ADD CONSTRAINT `pt_lrc_payload_ck`
            CHECK (
                (
                    `type_snapshot` = 'text'
                    AND `text_content_snapshot` IS NOT NULL
                    AND CHAR_LENGTH(TRIM(`text_content_snapshot`)) > 0
                    AND `media_disk_snapshot` IS NULL
                    AND `media_path_snapshot` IS NULL
                )
                OR
                (
                    `type_snapshot` IN ('audio', 'image', 'video')
                    AND `text_content_snapshot` IS NULL
                    AND `media_disk_snapshot` IS NOT NULL
                    AND CHAR_LENGTH(TRIM(`media_disk_snapshot`)) > 0
                    AND `media_path_snapshot` IS NOT NULL
                    AND CHAR_LENGTH(TRIM(`media_path_snapshot`)) > 0
                    AND LOWER(TRIM(`media_path_snapshot`)) NOT LIKE '%://%'
                    AND TRIM(`media_path_snapshot`) NOT LIKE '//%'
                )
            )
        SQL);
    }

    private function dropChecks(): void
    {
        DB::statement('ALTER TABLE `placement_test_level_result_contents` DROP CONSTRAINT `pt_lrc_payload_ck`');
        DB::statement('ALTER TABLE `placement_test_level_result_contents` DROP CONSTRAINT `pt_lrc_type_ck`');
        DB::statement('ALTER TABLE `placement_test_level_questions` DROP CONSTRAINT `pt_lq_points_ck`');
        DB::statement('ALTER TABLE `placement_test_level_results` DROP CONSTRAINT `pt_lr_correct_points_ck`');
        DB::statement('ALTER TABLE `placement_test_level_results` DROP CONSTRAINT `pt_lr_total_points_ck`');
        DB::statement('ALTER TABLE `placement_test_questions` DROP CONSTRAINT `pt_q_points_ck`');
        DB::statement('ALTER TABLE `placement_test_question_contents` DROP CONSTRAINT `pt_qc_payload_ck`');
        DB::statement('ALTER TABLE `placement_test_question_contents` DROP CONSTRAINT `pt_qc_type_ck`');
    }

    private function assertScoringDataCanBeHardened(): void
    {
        $invalidMasterQuestions = DB::table('placement_test_questions')
            ->where(function ($query): void {
                $query->whereNull('points')->orWhere('points', '<=', 0);
            })
            ->exists();
        $invalidLevelResults = DB::table('placement_test_level_results')
            ->where(function ($query): void {
                $query->whereNull('total_points_snapshot')
                    ->orWhereNull('correct_points')
                    ->orWhere('total_points_snapshot', '<=', 0)
                    ->orWhere('correct_points', '<', 0)
                    ->orWhereColumn('correct_points', '>', 'total_points_snapshot');
            })
            ->exists();
        $invalidLevelQuestions = DB::table('placement_test_level_questions')
            ->where(function ($query): void {
                $query->whereNull('points_snapshot')->orWhere('points_snapshot', '<=', 0);
            })
            ->exists();

        if ($invalidMasterQuestions || $invalidLevelResults || $invalidLevelQuestions) {
            throw new \RuntimeException(
                'Placement-test puan geçmişi eksik veya geçersiz. Otomatik puan atanmıyor; önce veriyi düzeltin ya da demo kayıtlarını açıkça temizleyin.',
            );
        }
    }
};
