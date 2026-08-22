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
        Schema::table('placement_test_level_results', function (Blueprint $table): void {
            $table->unique(
                ['id', 'placement_test_level_id'],
                'pt_lr_id_level_uq',
            );
        });

        Schema::table('placement_test_level_result_contents', function (Blueprint $table): void {
            $table->foreignId('placement_test_level_id')
                ->nullable()
                ->after('placement_test_level_result_id');
        });

        DB::statement(<<<'SQL'
            UPDATE `placement_test_level_result_contents` AS `content`
            INNER JOIN `placement_test_level_results` AS `result`
                ON `result`.`id` = `content`.`placement_test_level_result_id`
            SET `content`.`placement_test_level_id` = `result`.`placement_test_level_id`
            WHERE `content`.`placement_test_level_id` IS NULL
        SQL);

        if (DB::table('placement_test_level_result_contents')
            ->whereNull('placement_test_level_id')
            ->exists()) {
            throw new \RuntimeException('Placement-test içerik snapshot seviyeleri doldurulamadı.');
        }

        $this->ensureSourceContentIndex();

        Schema::table('placement_test_level_result_contents', function (Blueprint $table): void {
            $table->unsignedBigInteger('placement_test_level_id')->nullable(false)->change();
            $table->unique(
                ['id', 'placement_test_level_result_id'],
                'pt_lrc_id_result_uq',
            );
            $table->index(
                ['placement_test_level_result_id', 'placement_test_level_id'],
                'pt_lrc_result_level_ix',
            );
        });

        Schema::table('placement_test_level_result_contents', function (Blueprint $table): void {
            $table->foreign(
                ['placement_test_level_result_id', 'placement_test_level_id'],
                'pt_lrc_result_level_fk',
            )
                ->references(['id', 'placement_test_level_id'])
                ->on('placement_test_level_results')
                ->restrictOnDelete();
        });

        Schema::table('placement_test_level_questions', function (Blueprint $table): void {
            $table->index(
                ['placement_test_level_result_content_id', 'placement_test_level_result_id'],
                'pt_lq_content_result_ix',
            );
        });

        Schema::table('placement_test_level_questions', function (Blueprint $table): void {
            $table->foreign(
                ['placement_test_level_result_content_id', 'placement_test_level_result_id'],
                'pt_lq_content_result_fk',
            )
                ->references(['id', 'placement_test_level_result_id'])
                ->on('placement_test_level_result_contents')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_test_level_questions', function (Blueprint $table): void {
            $table->dropForeign('pt_lq_content_result_fk');
            $table->dropIndex('pt_lq_content_result_ix');
        });

        Schema::table('placement_test_level_result_contents', function (Blueprint $table): void {
            $table->dropForeign('pt_lrc_result_level_fk');
        });

        Schema::table('placement_test_level_result_contents', function (Blueprint $table): void {
            $table->dropIndex('pt_lrc_result_level_ix');
            $table->dropUnique('pt_lrc_id_result_uq');
            $table->dropColumn('placement_test_level_id');
        });

        Schema::table('placement_test_level_results', function (Blueprint $table): void {
            $table->dropUnique('pt_lr_id_level_uq');
        });
    }

    private function ensureSourceContentIndex(): void
    {
        $exists = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'placement_test_level_result_contents')
            ->where('INDEX_NAME', 'pt_lrc_content_source_ix')
            ->exists();

        if (! $exists) {
            Schema::table('placement_test_level_result_contents', function (Blueprint $table): void {
                $table->index('placement_test_question_content_id', 'pt_lrc_content_source_ix');
            });
        }
    }
};
