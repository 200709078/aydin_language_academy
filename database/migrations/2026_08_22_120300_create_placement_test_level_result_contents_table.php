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
        Schema::create('placement_test_level_result_contents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('placement_test_level_result_id');
            $table->foreignId('placement_test_question_content_id')->nullable();
            $table->string('type_snapshot', 16);
            $table->longText('text_content_snapshot')->nullable();
            $table->string('media_disk_snapshot', 32)->nullable();
            $table->string('media_path_snapshot', 2048)->nullable();
            $table->timestamps();

            $table->foreign('placement_test_level_result_id', 'pt_lrc_result_fk')
                ->references('id')
                ->on('placement_test_level_results')
                ->restrictOnDelete();
            $table->foreign('placement_test_question_content_id', 'pt_lrc_content_fk')
                ->references('id')
                ->on('placement_test_question_contents')
                ->nullOnDelete();
            $table->unique(
                ['placement_test_level_result_id', 'placement_test_question_content_id'],
                'pt_lrc_result_content_uq',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_test_level_result_contents');
    }
};
