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
        Schema::create('placement_test_level_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('placement_test_level_result_id');
            $table->foreignId('placement_test_question_id')->nullable();
            $table->unsignedSmallInteger('display_position');
            $table->text('question_text_snapshot');
            $table->json('options_snapshot');
            $table->unsignedSmallInteger('correct_option_snapshot');
            $table->unsignedSmallInteger('selected_option')->nullable();
            $table->string('answer_status', 16)->default('blank');
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->foreign('placement_test_level_result_id', 'pt_lq_level_result_fk')
                ->references('id')
                ->on('placement_test_level_results')
                ->restrictOnDelete();
            $table->foreign('placement_test_question_id', 'pt_lq_question_fk')
                ->references('id')
                ->on('placement_test_questions')
                ->nullOnDelete();
            $table->unique(
                ['placement_test_level_result_id', 'display_position'],
                'pt_lq_result_pos_uq',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_test_level_questions');
    }
};
