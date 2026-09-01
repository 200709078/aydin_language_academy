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
        Schema::create('achievement_entries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('achievement_year_id');
            $table->string('student_full_name');
            $table->string('name_permission_status', 20)->default('unknown');
            $table->date('name_permission_at')->nullable();
            $table->text('name_permission_note')->nullable();
            $table->string('university_name')->nullable();
            $table->string('department_name')->nullable();
            $table->string('achievement_type', 100)->nullable();
            $table->text('result_note')->nullable();
            $table->string('branch', 20)->nullable();
            $table->string('program_label', 100)->nullable();
            $table->text('verification_note')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('display_order')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('achievement_year_id', 'ach_entry_year_fk')
                ->references('id')
                ->on('achievement_years')
                ->restrictOnDelete();
            $table->foreign('verified_by', 'ach_entry_verifier_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('created_by', 'ach_entry_creator_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('published_by', 'ach_entry_publisher_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->index(['achievement_year_id', 'status', 'published_at'], 'ach_entry_year_status_pub_ix');
            $table->index(['achievement_year_id', 'display_order'], 'ach_entry_year_order_ix');
            $table->index('name_permission_status', 'ach_entry_name_permission_ix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_entries');
    }
};
