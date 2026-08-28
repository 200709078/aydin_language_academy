<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_attempt_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('exercise_attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('question_option_id')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->foreign('exercise_attempt_id', 'ex_att_ans_attempt_fk')
                ->references('id')
                ->on('exercise_attempts')
                ->cascadeOnDelete();
            $table->foreign('question_id', 'ex_att_ans_question_fk')
                ->references('id')
                ->on('questions')
                ->cascadeOnDelete();
            $table->foreign('question_option_id', 'ex_att_ans_option_fk')
                ->references('id')
                ->on('question_options')
                ->nullOnDelete();
            $table->unique(['exercise_attempt_id', 'question_id'], 'ex_att_ans_attempt_question_uq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_attempt_answers');
    }
};
