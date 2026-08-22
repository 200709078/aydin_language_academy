<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('placement_test_questions', function (Blueprint $table): void {
            $table->foreignId('placement_test_question_content_id')
                ->nullable()
                ->after('placement_test_level_id');
            $table->unsignedSmallInteger('content_position')
                ->nullable()
                ->after('placement_test_question_content_id');
            $table->decimal('points', 8, 2)
                ->nullable()
                ->after('question_text');

            $table->foreign('placement_test_question_content_id', 'pt_q_content_fk')
                ->references('id')
                ->on('placement_test_question_contents')
                ->restrictOnDelete();
            $table->unique(
                ['placement_test_question_content_id', 'content_position'],
                'pt_q_content_pos_uq',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_test_questions', function (Blueprint $table): void {
            $table->dropForeign('pt_q_content_fk');
            $table->dropUnique('pt_q_content_pos_uq');
            $table->dropColumn([
                'placement_test_question_content_id',
                'content_position',
                'points',
            ]);
        });
    }
};
