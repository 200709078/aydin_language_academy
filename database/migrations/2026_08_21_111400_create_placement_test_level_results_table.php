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
        Schema::create('placement_test_level_results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('placement_test_id');
            $table->foreignId('placement_test_level_id');
            $table->unsignedSmallInteger('question_count_snapshot');
            $table->decimal('pass_percentage_snapshot', 5, 2);
            $table->unsignedSmallInteger('correct_count')->default(0);
            $table->unsignedSmallInteger('wrong_count')->default(0);
            $table->unsignedSmallInteger('blank_count')->default(0);
            $table->decimal('score_percentage', 5, 2)->nullable();
            $table->string('result', 16)->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('placement_test_id', 'pt_lr_test_fk')
                ->references('id')
                ->on('placement_tests')
                ->restrictOnDelete();
            $table->foreign('placement_test_level_id', 'pt_lr_level_fk')
                ->references('id')
                ->on('placement_test_levels')
                ->restrictOnDelete();
            $table->unique(
                ['placement_test_id', 'placement_test_level_id'],
                'pt_lr_test_level_uq',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_test_level_results');
    }
};
