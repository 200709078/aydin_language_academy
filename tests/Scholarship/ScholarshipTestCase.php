<?php

namespace Tests\Scholarship;

use App\Models\ScholarshipBranch;
use App\Models\ScholarshipExamGroup;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipExamSession;
use App\Models\ScholarshipSchool;
use App\Models\ScholarshipStudentLevel;
use App\Models\User;
use App\Services\ScholarshipApplicationService;
use App\Services\ScholarshipCatalogService;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;
use Throwable;

abstract class ScholarshipTestCase extends TestCase
{
    protected User $admin;

    protected User $student;

    protected User $other;

    protected User $third;

    protected ScholarshipExamPeriod $period;

    protected ScholarshipExamSession $session;

    protected ScholarshipExamSession $alternative;

    protected ScholarshipBranch $branch;

    protected ScholarshipBranch $otherBranch;

    protected ScholarshipSchool $school;

    protected ScholarshipStudentLevel $level;

    protected ScholarshipExamGroup $group;

    protected ScholarshipApplicationService $applications;

    protected ScholarshipCatalogService $catalog;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Carbon::setTestNow('2035-01-10 12:00:00');
        \Carbon\CarbonImmutable::setTestNow('2035-01-10 12:00:00');
        $this->clearFixtures();
        $this->applications = app(ScholarshipApplicationService::class);
        $this->catalog = app(ScholarshipCatalogService::class);
        foreach (['admin', 'student', 'other', 'third'] as $name) {
            $this->$name = User::query()->create(['name' => 'Synthetic student', 'email' => $name.'@example.invalid', 'phone' => '0000000000', 'password' => 'unused-synthetic-password']);
        }
        $this->admin->forceFill(['type' => 'admin'])->save();
        $this->branch = $this->catalog->saveDefinition($this->admin, 'branch', ['code' => 'test-a', 'name' => 'Branch A']);
        $this->otherBranch = $this->catalog->saveDefinition($this->admin, 'branch', ['code' => 'test-b', 'name' => 'Branch B']);
        $this->school = $this->catalog->saveDefinition($this->admin, 'school', ['name' => 'Current school']);
        $this->level = $this->catalog->saveDefinition($this->admin, 'student_level', ['code' => 'grade-1', 'name' => '1. Sınıf']);
        $this->group = $this->catalog->saveDefinition($this->admin, 'exam_group', ['code' => 'high', 'name' => 'Lise']);
        $this->period = $this->catalog->savePeriod($this->admin, [
            'title' => 'Test period', 'applications_open_at' => '2035-01-01 00:00:00', 'applications_close_at' => '2035-01-20 23:59:59',
            'exam_starts_on' => '2035-01-25', 'exam_ends_on' => '2035-01-26', 'is_active' => true, 'applications_open' => true,
        ]);
        $session = ['period_id' => $this->period->id, 'branch_id' => $this->branch->id, 'exam_group_id' => $this->group->id,
            'exam_title' => 'Test exam', 'exam_date' => '2035-01-25', 'starts_at' => '08:00:00', 'ends_at' => '10:00:00', 'capacity' => 2];
        $this->session = $this->catalog->saveSession($this->admin, $session);
        $this->alternative = $this->catalog->saveSession($this->admin, [...$session, 'branch_id' => $this->otherBranch->id, 'starts_at' => '16:00:00', 'ends_at' => '18:00:00']);
    }

    protected function data(?ScholarshipExamSession $session = null): array
    {
        $session ??= $this->session;

        return ['period_id' => $session->period_id, 'session_id' => $session->id, 'school_id' => $this->school->id, 'student_level_id' => $this->level->id];
    }

    protected function rejects(callable $operation, string $exception = \Illuminate\Validation\ValidationException::class): Throwable
    {
        try {
            $operation();
        } catch (Throwable $error) {
            self::assertInstanceOf($exception, $error, $error->getMessage());

            return $error;
        }
        self::fail('Expected rejection: '.$exception);
    }

    protected function clearFixtures(): void
    {
        // Never use the ordinary Feature/RefreshDatabase setup or application DB.
        self::assertSame('scholarship_domain_test', DB::connection()->getDatabaseName());
        self::assertSame(getenv('ALA_SCHOLARSHIP_TEST_DIR').'/mysql.sock', DB::connection()->getConfig('unix_socket'));
        foreach (['scholarship_notifications', 'scholarship_applications', 'scholarship_exam_sessions', 'scholarship_exam_periods',
            'scholarship_exam_groups', 'scholarship_student_levels', 'scholarship_schools', 'scholarship_branches', 'users'] as $table) {
            DB::table($table)->delete();
        }
    }
}
