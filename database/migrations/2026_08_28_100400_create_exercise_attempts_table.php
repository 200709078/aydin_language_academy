<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_attempts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('exercise_id');
            $table->string('status', 20)->default('in_progress');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'ex_att_user_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('exercise_id', 'ex_att_exercise_fk')
                ->references('id')
                ->on('exercises')
                ->cascadeOnDelete();
            $table->index(['user_id', 'exercise_id', 'status'], 'ex_att_user_ex_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_attempts');
    }
};
