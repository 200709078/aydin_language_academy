<?php

namespace Tests\Scholarship;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ScholarshipAnswerCountsTest extends ScholarshipTestCase
{
    private mixed $originalMail;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalMail = Mail::getFacadeRoot();
    }

    protected function tearDown(): void
    {
        Mail::swap($this->originalMail);
        parent::tearDown();
    }

    public function test_counts_preserve_null_zero_visibility_contact_and_absence_rules(): void
    {
        Mail::fake();
        $application = $this->applications->create($this->student, $this->data());
        $this->rejects(fn () => $this->applications->updateResult($this->student, $application->id, ['correct_count' => 35]), AuthorizationException::class);
        $this->applications->updateResult($this->admin, $application->id, ['correct_count' => 35, 'wrong_count' => 0]);
        $fresh = $application->fresh();
        self::assertSame([35, 0, null, null], [$fresh->correct_count, $fresh->wrong_count, $fresh->blank_count, $fresh->score]);
        self::assertSame(['published' => false], $this->applications->forMember($this->student, $application->id)['result']);
        foreach (['correct_count', 'wrong_count', 'blank_count'] as $field) {
            self::assertArrayNotHasKey($field, $fresh->toArray());
        }
        $this->applications->updateResult($this->admin, $application->id, ['scholarship_percentage' => 0]);
        $this->applications->publish($this->admin, [$application->id], 'result');
        $result = $this->applications->forMember($this->student, $application->id)['result'];
        self::assertSame([true, null, 0, 35, 0, null], [$result['published'], $result['score'], $result['scholarship_percentage'], $result['correct_count'], $result['wrong_count'], $result['blank_count']]);
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $this->applications->updateResult($this->admin, $application->id, ['blank_count' => 5]);
        self::assertSame('unreached', $application->fresh()->result_contact_status);
        self::assertSame('reached', $application->fresh()->application_contact_status);
        self::assertTrue($application->fresh()->result_published);
        $this->applications->updateResult($this->admin, $application->id, ['attendance_status' => 'absent']);
        $fresh = $application->fresh();
        self::assertSame([0, 0, null, null, null], [$fresh->score, $fresh->scholarship_percentage, $fresh->correct_count, $fresh->wrong_count, $fresh->blank_count]);
        $this->rejects(fn () => $this->applications->updateResult($this->admin, $application->id, ['blank_count' => 0]));
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
    }

    public function test_migration_enforces_award_only_publication_and_count_constraints_and_can_roll_back(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $row = DB::table('scholarship_applications')->where('id', $application->id);
        $row->update(['scholarship_percentage' => 0, 'result_published' => true, 'correct_count' => 65535, 'wrong_count' => 0]);
        self::assertNull($application->fresh()->score);
        self::assertTrue($application->fresh()->result_published);
        foreach ([['correct_count' => -1], ['wrong_count' => 65536], ['scholarship_percentage' => null], ['attendance_status' => 'absent', 'score' => 0]] as $invalid) {
            $this->rejects(fn () => $row->update($invalid), QueryException::class);
        }
        $migration = require dirname(__DIR__, 2).'/database/migrations/2026_09_20_100000_add_answer_counts_to_scholarship_applications.php';
        $this->rejects(fn () => $migration->down(), RuntimeException::class);
        self::assertTrue(Schema::hasColumn('scholarship_applications', 'correct_count'));
        $row->update(['result_published' => false]);
        $migration->down();
        try {
            self::assertFalse(Schema::hasColumn('scholarship_applications', 'correct_count'));
            $this->rejects(fn () => $row->update(['result_published' => true]), QueryException::class);
        } finally {
            $migration->up();
        }
        $row->update(['result_published' => true, 'blank_count' => 0]);
        self::assertSame(0, $application->fresh()->blank_count);
    }
}
