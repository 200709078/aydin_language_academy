<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_branches', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 64);
            $table->string('name', 150);
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('code', 'sch_branch_code_uq');
            $table->index(['is_active', 'sort_order'], 'sch_branch_active_sort_ix');
        });

        Schema::create('scholarship_schools', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            // Different schools may share a name.
            $table->index('name', 'sch_school_name_ix');
            $table->index(['is_active', 'sort_order'], 'sch_school_active_sort_ix');
        });

        Schema::create('scholarship_student_levels', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 64);
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('code', 'sch_student_level_code_uq');
            $table->index(['is_active', 'sort_order'], 'sch_student_level_active_sort_ix');
        });

        Schema::create('scholarship_exam_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 64);
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('code', 'sch_exam_group_code_uq');
            $table->index(['is_active', 'sort_order'], 'sch_exam_group_active_sort_ix');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_exam_groups');
        Schema::dropIfExists('scholarship_student_levels');
        Schema::dropIfExists('scholarship_schools');
        Schema::dropIfExists('scholarship_branches');
    }
};
