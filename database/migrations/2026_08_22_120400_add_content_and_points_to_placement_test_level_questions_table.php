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
        Schema::table('placement_test_level_questions', function (Blueprint $table): void {
            $table->foreignId('placement_test_level_result_content_id')
                ->nullable()
                ->after('placement_test_question_id');
            $table->decimal('points_snapshot', 8, 2)
                ->nullable()
                ->after('correct_option_snapshot');

            $table->foreign('placement_test_level_result_content_id', 'pt_lq_content_fk')
                ->references('id')
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
            $table->dropForeign('pt_lq_content_fk');
            $table->dropColumn([
                'placement_test_level_result_content_id',
                'points_snapshot',
            ]);
        });
    }
};
