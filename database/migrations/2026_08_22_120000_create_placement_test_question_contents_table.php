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
        Schema::create('placement_test_question_contents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('placement_test_level_id');
            $table->string('type', 16);
            $table->longText('text_content')->nullable();
            $table->string('media_disk', 32)->nullable();
            $table->string('media_path', 2048)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('placement_test_level_id', 'pt_qc_level_fk')
                ->references('id')
                ->on('placement_test_levels')
                ->restrictOnDelete();
            $table->index(['placement_test_level_id', 'is_active'], 'pt_qc_level_active_ix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_test_question_contents');
    }
};
