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
        Schema::table('placement_test_level_results', function (Blueprint $table): void {
            $table->decimal('total_points_snapshot', 13, 2)
                ->nullable()
                ->after('pass_percentage_snapshot');
            $table->decimal('correct_points', 13, 2)
                ->nullable()
                ->after('total_points_snapshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_test_level_results', function (Blueprint $table): void {
            $table->dropColumn(['total_points_snapshot', 'correct_points']);
        });
    }
};
