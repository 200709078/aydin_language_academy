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
        Schema::create('placement_test_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('placement_test_level_id');
            $table->text('question_text');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('placement_test_level_id', 'pt_q_level_fk')
                ->references('id')
                ->on('placement_test_levels')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_test_questions');
    }
};
