<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_exam_periods', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            // Application windows and session times use the existing Europe/Istanbul timezone.
            $table->dateTime('applications_open_at');
            $table->dateTime('applications_close_at');
            $table->date('exam_starts_on');
            $table->date('exam_ends_on');
            $table->boolean('is_active')->default(false);
            $table->boolean('applications_open')->default(false);
            $table->timestamps();

            $table->index(['is_active', 'applications_open', 'applications_close_at'], 'sch_period_open_ix');
            $table->index(['exam_starts_on', 'exam_ends_on'], 'sch_period_exam_dates_ix');
        });

        DB::statement('ALTER TABLE scholarship_exam_periods ADD CONSTRAINT sch_period_application_dates_ck CHECK (applications_open_at < applications_close_at)');
        DB::statement('ALTER TABLE scholarship_exam_periods ADD CONSTRAINT sch_period_exam_dates_ck CHECK (exam_starts_on <= exam_ends_on)');

        Schema::create('scholarship_exam_sessions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('exam_group_id');
            $table->string('exam_title', 150);
            $table->date('exam_date');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->unsignedInteger('capacity');
            $table->boolean('is_active')->default(true);
            $table->dateTime('archived_at')->nullable();
            $table->timestamps();

            // The composite application FK also enforces its session's period.
            $table->unique(['id', 'period_id'], 'sch_session_id_period_uq');
            $table->unique(
                ['period_id', 'branch_id', 'exam_group_id', 'exam_title', 'exam_date', 'starts_at', 'ends_at'],
                'sch_session_definition_uq'
            );
            $table->index(['period_id', 'exam_date', 'starts_at'], 'sch_session_period_date_ix');
            $table->index(['period_id', 'is_active', 'archived_at'], 'sch_session_availability_ix');
            $table->index('branch_id', 'sch_session_branch_ix');
            $table->index('exam_group_id', 'sch_session_group_ix');

            $table->foreign('period_id', 'sch_session_period_fk')->references('id')->on('scholarship_exam_periods')->restrictOnDelete();
            $table->foreign('branch_id', 'sch_session_branch_fk')->references('id')->on('scholarship_branches')->restrictOnDelete();
            $table->foreign('exam_group_id', 'sch_session_group_fk')->references('id')->on('scholarship_exam_groups')->restrictOnDelete();
        });

        DB::statement('ALTER TABLE scholarship_exam_sessions ADD CONSTRAINT sch_session_time_ck CHECK (starts_at >= \'00:00:00\' AND ends_at < \'24:00:00\' AND starts_at < ends_at)');
        DB::statement('ALTER TABLE scholarship_exam_sessions ADD CONSTRAINT sch_session_capacity_ck CHECK (capacity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_exam_sessions');
        Schema::dropIfExists('scholarship_exam_periods');
    }
};
