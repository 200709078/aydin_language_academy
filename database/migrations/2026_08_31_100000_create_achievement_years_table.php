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
        Schema::create('achievement_years', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by', 'ach_year_creator_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('published_by', 'ach_year_publisher_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->unique('year', 'ach_year_year_uq');
            $table->index(['status', 'published_at'], 'ach_year_status_pub_ix');
            $table->index(['sort_order', 'year'], 'ach_year_order_year_ix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_years');
    }
};
