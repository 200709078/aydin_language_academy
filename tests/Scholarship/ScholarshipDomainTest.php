<?php

namespace Tests\Scholarship;

use App\Models\ScholarshipApplication;
use App\Models\ScholarshipBranch;
use App\Models\ScholarshipExamGroup;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipExamSession;
use App\Models\ScholarshipNotification;
use App\Models\ScholarshipSchool;
use App\Models\ScholarshipStudentLevel;
use App\Models\User;
use Database\Seeders\ScholarshipDemoSeeder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use LogicException;

class ScholarshipDomainTest extends ScholarshipTestCase
{
    public function test_application_keeps_period_snapshots_but_uses_current_account_contacts(): void
    {
        $application = $this->applications->create($this->student, $this->data());

        self::assertSame('pending', $application->status);
        self::assertFalse($application->application_published);
        self::assertFalse($application->result_published);
        self::assertNull($application->score);
        self::assertNull($application->scholarship_percentage);
        self::assertSame('unmarked', $application->attendance_status);
        self::assertTrue($application->period->is($this->period));
        self::assertTrue($application->session->is($this->session));
        self::assertTrue($application->school->is($this->school));
        self::assertTrue($application->studentLevel->is($this->level));
        self::assertTrue($application->user->is($this->student));
        self::assertTrue($this->student->scholarshipApplications()->first()->is($application));
        // Grade 1 and the independently chosen Lise exam group are valid together.
        self::assertSame('1. Sınıf', $application->student_level_name_snapshot);
        self::assertSame('Lise', $application->session->examGroup->name);

        $this->applications->approve($this->admin, $application->id);
        $this->student->update(['name' => 'Changed account name', 'email' => 'changed@example.invalid', 'phone' => '1111111111']);
        $this->catalog->saveDefinition($this->admin, 'school', ['name' => 'Renamed school'], $this->school->id);
        $this->catalog->saveDefinition($this->admin, 'student_level', ['name' => 'Renamed level'], $this->level->id);

        $application->refresh();
        self::assertSame('Synthetic student', $application->student_name_snapshot);
        self::assertSame('Current school', $application->school_name_snapshot);
        self::assertSame('1. Sınıf', $application->student_level_name_snapshot);
        self::assertSame('changed@example.invalid', $application->user->email);
        self::assertSame('1111111111', $application->user->phone);
        self::assertSame('Synthetic student', $this->applications->forMember($this->student, $application->id)['student']['name']);
    }

    public function test_one_current_application_per_period_allows_deletion_reapplication_and_later_periods(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $number = $application->application_number;
        $this->rejects(fn () => $this->applications->create($this->student, $this->data($this->alternative)));
        $moved = $this->applications->update($this->student, $application->id, ['session_id' => $this->alternative->id]);
        self::assertSame($number, $moved->application_number);
        self::assertSame(0, $this->session->applications()->count());
        $this->applications->delete($this->student, $application->id);
        $replacement = $this->applications->create($this->student, $this->data());
        self::assertNotSame($number, $replacement->application_number);
        self::assertSame(1, ScholarshipApplication::query()->count());

        $laterPeriod = $this->catalog->savePeriod($this->admin, [
            'title' => 'Next year', 'applications_open_at' => '2036-01-01 00:00:00', 'applications_close_at' => '2036-01-20 23:59:59',
            'exam_starts_on' => '2036-01-25', 'exam_ends_on' => '2036-01-26', 'is_active' => true, 'applications_open' => true,
        ]);
        $laterSession = $this->catalog->saveSession($this->admin, [
            ...$this->sessionData(), 'period_id' => $laterPeriod->id, 'exam_date' => '2036-01-25',
        ]);
        \Illuminate\Support\Carbon::setTestNow('2036-01-10 12:00:00');
        \Carbon\CarbonImmutable::setTestNow('2036-01-10 12:00:00');
        $later = $this->applications->create($this->student, $this->data($laterSession));
        self::assertNotSame($replacement->period_id, $later->period_id);
        self::assertCount(2, $this->applications->listForMember($this->student));
        // Distinct emails with a shared guardian phone remain separate accounts.
        self::assertSame($this->student->phone, $this->other->phone);
        self::assertNotSame($this->student->email, $this->other->email);
        $otherApplication = $this->applications->create($this->other, $this->data($laterSession));
        self::assertNotSame($later->user_id, $otherApplication->user_id);
    }

    public function test_capacity_counts_pending_and_approved_and_admin_transfers_are_atomic(): void
    {
        $first = $this->applications->create($this->student, $this->data());
        $this->applications->create($this->other, $this->data());
        $this->applications->approve($this->admin, $first->id);
        $this->applications->publish($this->admin, [$first->id], 'application');
        self::assertSame(2, $this->session->applications()->count());
        $this->rejects(fn () => $this->applications->create($this->third, $this->data()));
        $this->rejects(fn () => $this->catalog->saveSession($this->admin, ['capacity' => 1], $this->session->id));

        $this->catalog->saveSession($this->admin, ['capacity' => 1], $this->alternative->id);
        $this->applications->create($this->third, $this->data($this->alternative));
        $this->rejects(fn () => $this->applications->update($this->admin, $first->id, ['session_id' => $this->alternative->id]));
        self::assertSame($this->session->id, $first->fresh()->session_id);
        self::assertSame(2, $this->session->applications()->count());
        self::assertSame(1, $this->alternative->applications()->count());

        $this->catalog->saveSession($this->admin, ['capacity' => 2], $this->alternative->id);
        $moved = $this->applications->update($this->admin, $first->id, ['session_id' => $this->alternative->id]);
        self::assertSame('approved', $moved->status);
        self::assertTrue($moved->application_published);
        self::assertSame(1, $this->session->applications()->count());
        self::assertSame(2, $this->alternative->applications()->count());
        // Updating an existing occupant in a full session does not require an extra seat.
        $this->applications->update($this->admin, $first->id, ['student_name' => 'Corrected name']);
        $this->catalog->saveSession($this->admin, ['capacity' => 1], $this->session->id);
        self::assertSame(3, (int) $this->period->sessions()->sum('capacity'));
    }

    public function test_period_flags_and_application_dates_lock_member_writes(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $open = ['is_active' => true, 'applications_open' => true, 'applications_open_at' => '2035-01-01 00:00:00', 'applications_close_at' => '2035-01-20 23:59:59'];
        foreach ([
            ['applications_open' => false],
            ['is_active' => false],
            ['applications_open_at' => '2035-01-11 00:00:00'],
            ['applications_close_at' => '2035-01-09 23:59:59'],
        ] as $closed) {
            $this->catalog->savePeriod($this->admin, [...$open, ...$closed], $this->period->id);
            $this->rejects(fn () => $this->applications->create($this->other, $this->data()));
            $this->rejects(fn () => $this->applications->update($this->student, $application->id, ['student_name' => 'Blocked change']));
            $this->rejects(fn () => $this->applications->delete($this->student, $application->id));
            $view = $this->applications->forMember($this->student, $application->id);
            self::assertFalse($view['can_edit']);
            self::assertFalse($view['can_delete']);
            self::assertSame('applications_closed', $view['restriction']);
        }
        $this->applications->update($this->admin, $application->id, ['student_name' => 'Admin correction']);
        self::assertSame('Admin correction', $application->fresh()->student_name_snapshot);
        $this->catalog->savePeriod($this->admin, $open, $this->period->id);
        self::assertTrue($this->applications->forMember($this->student, $application->id)['can_edit']);
    }

    public function test_unpublished_approval_locks_member_without_disclosing_the_decision(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->approve($this->admin, $application->id);
        $this->rejects(fn () => $this->applications->update($this->student, $application->id, ['session_id' => $this->alternative->id]));
        $this->rejects(fn () => $this->applications->delete($this->student, $application->id));
        $view = $this->applications->forMember($this->student, $application->id);
        self::assertSame('under_review', $view['application']['state']);
        self::assertNull($view['application']['arrival_at']);
        self::assertFalse($view['can_edit']);
        self::assertStringNotContainsString('approved', json_encode($view));

        $this->applications->publish($this->admin, [$application->id], 'application');
        $published = $this->applications->forMember($this->student, $application->id);
        self::assertSame('approved', $published['application']['state']);
        self::assertSame('2035-01-25 07:30:00', $published['application']['arrival_at']);
        $this->applications->update($this->admin, $application->id, ['session_id' => $this->alternative->id]);
        $changed = $this->applications->forMember($this->student, $application->id);
        self::assertSame($this->alternative->id, $changed['session']['id']);
        self::assertSame('2035-01-25 15:30:00', $changed['application']['arrival_at']);
        self::assertFalse($changed['can_delete']);
        $this->applications->delete($this->admin, $application->id);
        self::assertFalse(ScholarshipApplication::query()->whereKey($application->id)->exists());
    }

    public function test_suspension_and_archive_apply_only_to_the_selected_session(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        foreach (['suspended', 'archived'] as $state) {
            if ($state === 'suspended') {
                $this->catalog->saveSession($this->admin, ['is_active' => false], $this->session->id);
            } else {
                $this->catalog->saveSession($this->admin, ['is_active' => true], $this->session->id);
                $this->catalog->archiveSession($this->admin, $this->session->id);
            }
            $this->rejects(fn () => $this->applications->create($this->other, $this->data()));
            $this->rejects(fn () => $this->applications->update($this->student, $application->id, ['session_id' => $this->alternative->id]));
            $this->rejects(fn () => $this->applications->delete($this->student, $application->id));
            self::assertSame($state, $this->applications->forMember($this->student, $application->id)['restriction']);
            self::assertSame(1, $this->session->applications()->count());
        }
        $this->applications->create($this->other, $this->data($this->alternative));
        $this->applications->update($this->admin, $application->id, ['session_id' => $this->alternative->id]);
        self::assertSame(2, $this->alternative->applications()->count());
        self::assertSame(0, $this->session->applications()->count());
        $this->catalog->archiveSession($this->admin, $this->session->id, false);
        self::assertNull($this->session->fresh()->archived_at);
    }

    public function test_invalid_or_inactive_selections_and_past_sessions_reject_new_applications(): void
    {
        foreach ([['branch', $this->branch], ['exam_group', $this->group], ['school', $this->school], ['student_level', $this->level]] as [$type, $definition]) {
            $this->catalog->saveDefinition($this->admin, $type, ['is_active' => false], $definition->id);
            $this->rejects(fn () => $this->applications->create($this->student, $this->data()));
            $this->catalog->saveDefinition($this->admin, $type, ['is_active' => true], $definition->id);
        }
        $this->rejects(fn () => $this->applications->create($this->student, [...$this->data(), 'school_id' => 99999999]));
        $this->catalog->savePeriod($this->admin, ['applications_close_at' => '2035-01-26 23:59:59'], $this->period->id);
        \Illuminate\Support\Carbon::setTestNow('2035-01-25 08:00:00');
        \Carbon\CarbonImmutable::setTestNow('2035-01-25 08:00:00');
        $this->rejects(fn () => $this->applications->create($this->student, $this->data()));
        self::assertNotNull($this->applications->create($this->student, $this->data($this->alternative))->id);
    }

    public function test_score_and_scholarship_validate_original_integer_input_and_preserve_null_zero_distinction(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        foreach ([-1, 101, 12.5, 12.0, '12.0', '1e1', true] as $score) {
            $this->rejects(fn () => $this->applications->updateResult($this->admin, $application->id, ['score' => $score]));
        }
        foreach ([5, 105, -10, 10.0, '10.0'] as $award) {
            $this->rejects(fn () => $this->applications->updateResult($this->admin, $application->id, ['scholarship_percentage' => $award]));
        }
        self::assertNull($application->fresh()->score);
        foreach (range(0, 100, 10) as $award) {
            $this->applications->updateResult($this->admin, $application->id, ['score' => '0', 'scholarship_percentage' => $award]);
            self::assertSame($award, $application->fresh()->scholarship_percentage);
        }
        $this->applications->updateResult($this->admin, $application->id, ['score' => 100, 'scholarship_percentage' => 0]);
        self::assertSame(100, $application->fresh()->score);
        $this->applications->updateResult($this->admin, $application->id, ['score' => null, 'scholarship_percentage' => null]);
        self::assertNull($application->fresh()->score);
        self::assertNull($application->fresh()->scholarship_percentage);
    }

    public function test_absence_sets_zeros_and_correction_does_not_reuse_an_automatic_result(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->updateResult($this->admin, $application->id, ['attendance_status' => 'absent']);
        self::assertSame(0, $application->fresh()->score);
        self::assertSame(0, $application->fresh()->scholarship_percentage);
        $this->rejects(fn () => $this->applications->updateResult($this->admin, $application->id, ['score' => 10]));
        $this->applications->publish($this->admin, [$application->id], 'result');
        $this->rejects(fn () => $this->applications->updateResult($this->admin, $application->id, ['attendance_status' => 'attended']));
        self::assertSame('absent', $application->fresh()->attendance_status);
        $this->applications->publish($this->admin, [$application->id], 'result', false);
        $this->applications->updateResult($this->admin, $application->id, ['attendance_status' => 'attended']);
        self::assertNull($application->fresh()->score);
        self::assertNull($application->fresh()->scholarship_percentage);
        $this->applications->updateResult($this->admin, $application->id, ['score' => 0, 'scholarship_percentage' => 0]);
        $this->applications->publish($this->admin, [$application->id], 'result');
        self::assertSame('attended', $this->applications->forMember($this->student, $application->id)['result']['attendance_status']);
    }

    public function test_bulk_publication_skips_missing_or_incomplete_records_and_preserves_visibility(): void
    {
        $ready = $this->applications->create($this->student, $this->data());
        $incomplete = $this->applications->create($this->other, $this->data());
        $this->applications->updateResult($this->admin, $ready->id, ['score' => 0, 'scholarship_percentage' => 0]);
        $this->applications->updateResult($this->admin, $incomplete->id, ['score' => 90]);
        self::assertSame(['published' => false], $this->applications->forMember($this->other, $incomplete->id)['result']);
        self::assertArrayNotHasKey('score', $incomplete->fresh()->toArray());
        self::assertArrayNotHasKey('status', $incomplete->fresh()->toArray());
        $result = $this->applications->publish($this->admin, [$incomplete->id, $ready->id, 99999999, $ready->id], 'result');
        self::assertSame([$ready->id], $result['updated']);
        self::assertArrayHasKey($incomplete->id, $result['skipped']);
        self::assertArrayHasKey(99999999, $result['skipped']);
        self::assertSame(0, $this->applications->forMember($this->student, $ready->id)['result']['score']);
        $this->rejects(fn () => $this->applications->updateResult($this->admin, $ready->id, ['score' => null]));
        $this->rejects(fn () => $this->applications->updateResult($this->admin, $ready->id, ['scholarship_percentage' => null]));
        self::assertTrue($ready->fresh()->result_published);
        $later = $this->applications->create($this->third, $this->data($this->alternative));
        self::assertFalse($later->result_published);
        $this->rejects(fn () => $this->applications->publish($this->admin, [$ready->id], 'unknown'));
        $this->applications->updateResult($this->admin, $ready->id, ['score' => 80, 'scholarship_percentage' => 50]);
        self::assertSame(80, $this->applications->listForMember($this->student)->first()['result']['score']);
    }

    public function test_contact_stages_reset_for_actual_changes_without_sending_or_erasing_delivery_status(): void
    {
        Mail::fake();
        Notification::fake();
        $application = $this->applications->create($this->student, $this->data());
        $delivery = ScholarshipNotification::query()->create([
            'application_id' => $application->id, 'phase' => 'application', 'channel' => 'email', 'status' => 'sent',
            'idempotency_key' => (string) Str::uuid(), 'recipient' => 'synthetic@example.invalid', 'body' => 'Never sent',
        ]);
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $this->applications->approve($this->admin, $application->id);
        self::assertSame('unreached', $application->fresh()->application_contact_status);
        self::assertSame('reached', $application->fresh()->result_contact_status);
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->approve($this->admin, $application->id);
        $this->applications->update($this->admin, $application->id, ['student_name' => $application->student_name_snapshot]);
        self::assertSame('reached', $application->fresh()->application_contact_status);
        $this->applications->updateResult($this->admin, $application->id, ['score' => 75, 'scholarship_percentage' => 50]);
        self::assertSame('unreached', $application->fresh()->result_contact_status);
        self::assertSame('reached', $application->fresh()->application_contact_status);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $this->applications->updateResult($this->admin, $application->id, ['score' => 75]);
        $this->applications->publish($this->admin, [$application->id], 'result');
        self::assertSame('reached', $application->fresh()->result_contact_status);
        $this->applications->updateResult($this->admin, $application->id, ['scholarship_percentage' => 70]);
        self::assertSame('unreached', $application->fresh()->result_contact_status);
        self::assertSame(70, $this->applications->forMember($this->student, $application->id)['result']['scholarship_percentage']);
        $this->catalog->saveSession($this->admin, ['starts_at' => '09:00:00', 'ends_at' => '11:00:00'], $this->session->id);
        self::assertSame('unreached', $application->fresh()->application_contact_status);
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->catalog->saveDefinition($this->admin, 'branch', ['name' => 'Corrected branch'], $this->branch->id);
        self::assertSame('unreached', $application->fresh()->application_contact_status);
        self::assertSame('Corrected branch', $this->applications->forMember($this->student, $application->id)['session']['branch']);
        self::assertSame('sent', $delivery->fresh()->status);
        self::assertSame(1, $application->notifications()->count());
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_ownership_admin_rights_and_write_field_allowlists_are_enforced(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->rejects(fn () => $this->applications->create(new User, $this->data()), AuthorizationException::class);
        foreach ([
            fn () => $this->applications->forMember($this->other, $application->id),
            fn () => $this->applications->update($this->other, $application->id, ['student_name' => 'Foreign edit']),
            fn () => $this->applications->delete($this->other, $application->id),
            fn () => $this->applications->approve($this->other, $application->id),
            fn () => $this->applications->updateResult($this->other, $application->id, ['score' => 100]),
            fn () => $this->applications->publish($this->other, [$application->id], 'result'),
            fn () => $this->applications->markContact($this->other, $application->id, 'result', true),
            fn () => $this->catalog->savePeriod($this->other, ['title' => 'Unauthorized'], $this->period->id),
        ] as $operation) {
            $this->rejects($operation, AuthorizationException::class);
        }
        self::assertCount(0, $this->applications->listForMember($this->other));
        $this->other->type = 'admin';
        $this->rejects(fn () => $this->applications->approve($this->other, $application->id), AuthorizationException::class);
        $this->rejects(fn () => $this->applications->create($this->third, [...$this->data(), 'user_id' => $this->student->id]));
        foreach ([['status' => 'approved'], ['score' => 100], ['result_published' => true], ['period_id' => 99], ['student_name' => '   ']] as $injection) {
            $this->rejects(fn () => $this->applications->update($this->student, $application->id, $injection));
        }
        self::assertSame('pending', $application->fresh()->status);
    }

    public function test_catalog_validates_dates_definition_identity_and_application_dependencies(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->rejects(fn () => $this->catalog->saveSession($this->admin, $this->sessionData()));
        foreach ([['exam_date' => '2035-01-27'], ['starts_at' => '12:00:00'], ['period_id' => 99999999], ['branch_id' => 99999999]] as $invalid) {
            $this->rejects(fn () => $this->catalog->saveSession($this->admin, $invalid, $this->session->id));
        }
        $this->rejects(fn () => $this->catalog->savePeriod($this->admin, ['exam_starts_on' => '2035-01-26'], $this->period->id));
        $this->rejects(fn () => $this->catalog->savePeriod($this->admin, ['applications_close_at' => '2034-12-31 00:00:00'], $this->period->id));
        $this->rejects(fn () => $this->catalog->deletePeriod($this->admin, $this->period->id));
        $this->rejects(fn () => $this->catalog->deleteSession($this->admin, $this->session->id));
        foreach ([['branch', $this->branch], ['exam_group', $this->group], ['school', $this->school], ['student_level', $this->level]] as [$type, $definition]) {
            $this->rejects(fn () => $this->catalog->deleteDefinition($this->admin, $type, $definition->id));
        }
        $this->applications->delete($this->admin, $application->id);
        $this->catalog->deleteSession($this->admin, $this->session->id);
        self::assertNull($this->session->fresh());
        $this->catalog->deleteDefinition($this->admin, 'school', $this->school->id);
        self::assertNull($this->school->fresh());
    }

    public function test_application_delete_cascades_only_delivery_records_and_account_delete_preserves_snapshots(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $other = $this->applications->create($this->other, $this->data($this->alternative));
        ScholarshipNotification::query()->create([
            'application_id' => $application->id, 'phase' => 'result', 'channel' => 'whatsapp',
            'idempotency_key' => (string) Str::uuid(), 'recipient' => '0000000000', 'body' => 'Synthetic only',
        ]);
        $this->applications->delete($this->student, $application->id);
        self::assertSame(0, ScholarshipNotification::query()->count());
        self::assertNotNull($this->student->fresh());
        self::assertNotNull($other->fresh());
        self::assertNotNull($this->session->fresh());
        $this->other->delete();
        self::assertNull($other->fresh()->user_id);
        self::assertSame('Synthetic student', $other->fresh()->student_name_snapshot);
    }

    public function test_development_seed_is_idempotent_and_preserves_admin_edits_and_deletions(): void
    {
        $this->clearFixtures();
        (new ScholarshipDemoSeeder)->run();
        self::assertSame(3, ScholarshipBranch::query()->count());
        self::assertSame(2, ScholarshipSchool::query()->count());
        self::assertSame(14, ScholarshipStudentLevel::query()->count());
        self::assertSame(4, ScholarshipExamGroup::query()->count());
        self::assertSame(9, ScholarshipExamSession::query()->count());
        $period = ScholarshipExamPeriod::query()->sole();
        self::assertFalse($period->is_active);
        self::assertFalse($period->applications_open);
        $period->update(['title' => 'Administrator renamed period']);
        $branch = ScholarshipBranch::query()->first();
        $branch->update(['name' => 'Administrator branch', 'is_active' => false]);
        ScholarshipSchool::query()->first()->update(['name' => 'Administrator school']);
        ScholarshipExamSession::query()->first()->delete();
        (new ScholarshipDemoSeeder)->run();
        self::assertSame(1, ScholarshipExamPeriod::query()->count());
        self::assertSame(8, ScholarshipExamSession::query()->count());
        self::assertSame(2, ScholarshipSchool::query()->count());
        self::assertFalse($branch->fresh()->is_active);
        self::assertSame('Administrator branch', $branch->fresh()->name);
        self::assertSame(0, User::query()->count());
        self::assertSame(0, ScholarshipApplication::query()->count());
        app()->instance('env', 'production');
        try {
            $this->rejects(fn () => (new ScholarshipDemoSeeder)->run(), LogicException::class);
        } finally {
            app()->instance('env', 'testing');
        }
    }

    private function sessionData(): array
    {
        return [
            'period_id' => $this->period->id, 'branch_id' => $this->branch->id, 'exam_group_id' => $this->group->id,
            'exam_title' => 'Test exam', 'exam_date' => '2035-01-25', 'starts_at' => '08:00:00', 'ends_at' => '10:00:00', 'capacity' => 2,
        ];
    }
}
