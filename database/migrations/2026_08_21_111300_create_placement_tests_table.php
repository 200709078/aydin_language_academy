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
        Schema::create('placement_tests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('status', 32)->default('in_progress');
            $table->foreignId('result_level_id')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'pt_test_user_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('result_level_id', 'pt_test_result_level_fk')
                ->references('id')
                ->on('placement_test_levels')
                ->restrictOnDelete();
            $table->foreign('approved_by', 'pt_test_approver_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->index(['user_id', 'status'], 'pt_test_user_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_tests');
    }
};
