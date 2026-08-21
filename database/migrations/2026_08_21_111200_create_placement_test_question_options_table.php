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
        Schema::create('placement_test_question_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('placement_test_question_id');
            $table->text('option_text');
            $table->unsignedSmallInteger('display_position');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('placement_test_question_id', 'pt_qo_question_fk')
                ->references('id')
                ->on('placement_test_questions')
                ->restrictOnDelete();
            $table->unique(
                ['placement_test_question_id', 'display_position'],
                'pt_qo_question_pos_uq',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_test_question_options');
    }
};
