<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace the legacy fixed five-answer schema with display-ordered options.
     *
     * The legacy questions are demo data. There are no persisted legacy exercise
     * attempts to preserve, so the table is intentionally rebuilt instead of
     * carrying answer1...answer5 data forward.
     */
    public function up(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exercise_id')
                ->constrained('exercises')
                ->cascadeOnDelete();
            $table->longText('question');
            $table->longText('image')->nullable();
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')
                ->constrained('questions')
                ->cascadeOnDelete();
            $table->longText('option_text');
            $table->unsignedSmallInteger('display_position');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['question_id', 'display_position'], 'q_opt_question_position_uq');
        });
    }

    /**
     * Restore the prior schema only. The intentionally removed demo data cannot
     * be reconstructed by a rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('exercise_id');
            $table->longText('question');
            $table->longText('image')->nullable();
            $table->string('answer1');
            $table->string('answer2');
            $table->string('answer3');
            $table->string('answer4');
            $table->string('answer5');
            $table->enum('correct_answer', ['answer1', 'answer2', 'answer3', 'answer4', 'answer5']);
            $table->foreign('exercise_id')->references('id')->on('exercises')->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
