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
        Schema::create('placement_test_levels', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 10)->unique();
            $table->unsignedSmallInteger('sequence')->unique();
            $table->boolean('has_exam')->default(true);
            $table->unsignedSmallInteger('question_count')->nullable();
            $table->unsignedTinyInteger('pass_percentage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_test_levels');
    }
};
