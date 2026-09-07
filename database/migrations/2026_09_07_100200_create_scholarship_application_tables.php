<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_applications', function (Blueprint $table): void {
            $table->id();
            $table->string('application_number', 32);
            // Nullable only to preserve applications when Jetstream deletes an account.
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('period_id');
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('student_level_id');
            // Period-specific student data; contact details still come from the current User.
            $table->string('student_name_snapshot');
            $table->string('school_name_snapshot');
            $table->string('student_level_name_snapshot', 100);
            $table->string('status', 20)->default('pending');
            $table->boolean('application_published')->default(false);
            $table->string('application_contact_status', 20)->default('unreached');
            $table->string('attendance_status', 20)->default('unmarked');
            $table->unsignedTinyInteger('score')->nullable();
            $table->unsignedTinyInteger('scholarship_percentage')->nullable();
            $table->boolean('result_published')->default(false);
            $table->string('result_contact_status', 20)->default('unreached');
            $table->timestamps();

            $table->unique('application_number', 'sch_app_number_uq');
            $table->unique(['user_id', 'period_id'], 'sch_app_user_period_uq');
            $table->index(['session_id', 'period_id'], 'sch_app_session_period_ix');
            $table->index('school_id', 'sch_app_school_ix');
            $table->index('student_level_id', 'sch_app_student_level_ix');
            $table->index(['period_id', 'status'], 'sch_app_period_status_ix');
            $table->index(['period_id', 'attendance_status'], 'sch_app_period_attendance_ix');
            $table->index(['period_id', 'score'], 'sch_app_period_score_ix');
            $table->index(['period_id', 'scholarship_percentage'], 'sch_app_period_scholarship_ix');
            $table->index(['period_id', 'application_contact_status'], 'sch_app_application_contact_ix');
            $table->index(['period_id', 'result_contact_status'], 'sch_app_result_contact_ix');

            $table->foreign('user_id', 'sch_app_user_fk')->references('id')->on('users')->nullOnDelete();
            $table->foreign('period_id', 'sch_app_period_fk')->references('id')->on('scholarship_exam_periods')->restrictOnDelete();
            $table->foreign(['session_id', 'period_id'], 'sch_app_session_period_fk')
                ->references(['id', 'period_id'])->on('scholarship_exam_sessions')->restrictOnDelete();
            $table->foreign('school_id', 'sch_app_school_fk')->references('id')->on('scholarship_schools')->restrictOnDelete();
            $table->foreign('student_level_id', 'sch_app_student_level_fk')->references('id')->on('scholarship_student_levels')->restrictOnDelete();
        });

        DB::statement("ALTER TABLE scholarship_applications ADD CONSTRAINT sch_app_status_ck CHECK (status IN ('pending', 'approved'))");
        DB::statement("ALTER TABLE scholarship_applications ADD CONSTRAINT sch_app_application_contact_ck CHECK (application_contact_status IN ('unreached', 'reached'))");
        DB::statement("ALTER TABLE scholarship_applications ADD CONSTRAINT sch_app_result_contact_ck CHECK (result_contact_status IN ('unreached', 'reached'))");
        DB::statement("ALTER TABLE scholarship_applications ADD CONSTRAINT sch_app_attendance_ck CHECK (attendance_status IN ('unmarked', 'attended', 'absent'))");
        DB::statement('ALTER TABLE scholarship_applications ADD CONSTRAINT sch_app_score_ck CHECK (score IS NULL OR score BETWEEN 0 AND 100)');
        DB::statement('ALTER TABLE scholarship_applications ADD CONSTRAINT sch_app_scholarship_ck CHECK (scholarship_percentage IS NULL OR scholarship_percentage IN (0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100))');
        DB::statement('ALTER TABLE scholarship_applications ADD CONSTRAINT sch_app_result_complete_ck CHECK (result_published = 0 OR (score IS NOT NULL AND scholarship_percentage IS NOT NULL))');
        DB::statement("ALTER TABLE scholarship_applications ADD CONSTRAINT sch_app_absent_result_ck CHECK (attendance_status <> 'absent' OR (score IS NOT NULL AND score = 0 AND scholarship_percentage IS NOT NULL AND scholarship_percentage = 0))");

        Schema::create('scholarship_notifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->string('phase', 20);
            $table->string('channel', 20);
            $table->string('status', 20)->default('queued');
            // One logical delivery keeps its key and message across retries.
            $table->uuid('idempotency_key');
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('provider', 64)->nullable();
            $table->string('provider_message_id')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->dateTime('queued_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->unique('idempotency_key', 'sch_notification_idempotency_uq');
            $table->index(['application_id', 'phase', 'channel'], 'sch_notification_application_ix');
            $table->index(['status', 'queued_at'], 'sch_notification_queue_ix');
            $table->index(['provider', 'provider_message_id'], 'sch_notification_provider_ix');
            // Explicit application deletion also removes its delivery records.
            $table->foreign('application_id', 'sch_notification_application_fk')
                ->references('id')->on('scholarship_applications')->cascadeOnDelete();
        });

        DB::statement("ALTER TABLE scholarship_notifications ADD CONSTRAINT sch_notification_phase_ck CHECK (phase IN ('application', 'result'))");
        DB::statement("ALTER TABLE scholarship_notifications ADD CONSTRAINT sch_notification_channel_ck CHECK (channel IN ('email', 'whatsapp'))");
        DB::statement("ALTER TABLE scholarship_notifications ADD CONSTRAINT sch_notification_status_ck CHECK (status IN ('queued', 'sent', 'failed'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_notifications');
        Schema::dropIfExists('scholarship_applications');
    }
};
