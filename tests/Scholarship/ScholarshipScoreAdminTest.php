<?php

namespace Tests\Scholarship;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ViewErrorBag;

class ScholarshipScoreAdminTest extends ScholarshipTestCase
{
    use InteractsWithAuthentication;
    use InteractsWithSession;
    use MakesHttpRequests;

    protected Application $app;

    private string $originalLocale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = app();
        $this->originalLocale = $this->app->getLocale();
        $this->app['auth']->forgetGuards();
        $this->flushSession();
        $this->withSession(['locale' => 'tr']);
        $this->app->setLocale('tr');
        $this->app['view']->share('errors', new ViewErrorBag);
    }

    protected function tearDown(): void
    {
        $this->app['auth']->forgetGuards();
        $this->flushSession();
        $this->app->setLocale($this->originalLocale);
        $this->app['view']->share('errors', new ViewErrorBag);
        parent::tearDown();
    }

    public function test_score_routes_require_admin_authentication(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $index = route('admin.scholarship.scores.index');
        $update = route('admin.scholarship.scores.update', $application);
        $this->get($index)->assertRedirect(route('login'));
        $this->patch($update, ['score' => 90, 'correct_count' => 40, 'wrong_count' => 0, 'blank_count' => 0])->assertRedirect(route('login'));
        $this->actingAs($this->student)->get($index)->assertForbidden();
        $this->patch($update, ['score' => 90, 'correct_count' => 40, 'wrong_count' => 0, 'blank_count' => 0])->assertForbidden();
        self::assertNull($application->fresh()->score);
        self::assertNull($application->fresh()->correct_count);
    }

    public function test_score_list_sorts_and_filters_zero_separately_from_missing_scores(): void
    {
        $high = $this->applications->create($this->student, $this->data());
        $zero = $this->applications->create($this->other, $this->data());
        $missing = $this->applications->create($this->third, $this->data($this->alternative));
        $this->applications->updateResult($this->admin, $high->id, ['score' => 100]);
        $this->applications->updateResult($this->admin, $zero->id, ['score' => 0]);
        $index = route('admin.scholarship.scores.index');
        $page = $this->actingAs($this->admin)->get($index)->assertOk()
            ->assertSee(__('scholarship.score_management'))->assertSee('name="score"', false)
            ->assertSee('ala-action-confirmation', false)
            ->assertSee('name="correct_count"', false)->assertSee('name="wrong_count"', false)->assertSee('name="blank_count"', false);
        self::assertSame([$high->id, $zero->id, $missing->id], $page->viewData('applications')->pluck('id')->all());
        $ascending = $this->get(route('admin.scholarship.scores.index', ['sort' => 'score_asc']))->assertOk();
        self::assertSame([$zero->id, $high->id, $missing->id], $ascending->viewData('applications')->pluck('id')->all());
        $zeroPage = $this->get(route('admin.scholarship.scores.index', [
            'branch_id' => $this->branch->id, 'exam_group_id' => $this->group->id,
            'student_level_id' => $this->level->id, 'score_min' => 0, 'score_max' => 0,
        ]))->assertOk();
        self::assertSame([$zero->id], $zeroPage->viewData('applications')->pluck('id')->all());
        $missingPage = $this->get(route('admin.scholarship.scores.index', ['score_state' => 'missing']))->assertOk();
        self::assertSame([$missing->id], $missingPage->viewData('applications')->pluck('id')->all());
    }

    public function test_row_save_accepts_bounds_and_clear_without_changing_other_fields_or_publishing(): void
    {
        Mail::fake();
        Notification::fake();
        $application = $this->applications->create($this->student, $this->data());
        $filters = ['sort' => 'name', 'period_id' => $this->period->id];
        $update = route('admin.scholarship.scores.update', ['application' => $application->id, ...$filters]);
        $this->actingAs($this->admin)->patch($update, ['score' => '100', 'correct_count' => '35', 'wrong_count' => '10', 'blank_count' => '0', 'attendance_status' => 'absent',
            'scholarship_percentage' => 100, 'result_published' => true, 'status' => 'approved',
            'user_id' => $this->other->id, 'session_id' => $this->alternative->id])
            ->assertRedirect(route('admin.scholarship.scores.index', $filters).'#score-row-'.$application->id)
            ->assertSessionHasNoErrors()->assertSessionHas('modalSuccessContent');
        $fresh = $application->fresh();
        self::assertSame([100, 'unmarked', null, false, 'pending', $this->student->id, $this->session->id], [
            $fresh->score, $fresh->attendance_status, $fresh->scholarship_percentage, $fresh->result_published,
            $fresh->status, $fresh->user_id, $fresh->session_id,
        ]);
        self::assertSame([35, 10, 0], [$fresh->correct_count, $fresh->wrong_count, $fresh->blank_count]);
        self::assertSame(['published' => false], $this->applications->forMember($this->student, $application->id)['result']);
        $this->patch($update, ['score' => '0'])->assertRedirect()->assertSessionHasNoErrors();
        self::assertSame(0, $application->fresh()->score);
        $this->patch($update, ['score' => ''])->assertRedirect()->assertSessionHasNoErrors();
        self::assertNull($application->fresh()->score);
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
        Notification::assertNothingSent();
    }

    public function test_invalid_missing_and_absent_student_scores_are_rejected(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $update = route('admin.scholarship.scores.update', $application);
        $this->actingAs($this->admin)->from(route('admin.scholarship.scores.index'));
        foreach (['12.5', -1, 101] as $invalid) {
            $this->patch($update, ['score' => $invalid])->assertSessionHasErrors('score');
        }
        $this->patch($update.'?score=99', [])->assertSessionHasErrors('score');
        self::assertNull($application->fresh()->score);
        foreach (['correct_count' => -1, 'wrong_count' => '1.5', 'blank_count' => 65536] as $field => $invalid) {
            $this->patch($update, ['score' => 50, $field => $invalid])->assertSessionHasErrors($field);
        }
        self::assertNull($application->fresh()->score);

        $this->applications->updateResult($this->admin, $application->id, ['attendance_status' => 'absent']);
        $this->patch($update, ['score' => 70, 'attendance_status' => 'attended'])->assertSessionHasErrors('score');
        $fresh = $application->fresh();
        self::assertSame(['absent', 0, 0], [$fresh->attendance_status, $fresh->score, $fresh->scholarship_percentage]);
    }

    public function test_published_score_and_counts_can_be_cleared_and_reset_only_result_contact(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->updateResult($this->admin, $application->id, [
            'attendance_status' => 'attended', 'score' => 60, 'scholarship_percentage' => 40,
        ]);
        $this->applications->publish($this->admin, [$application->id], 'result');
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $index = route('admin.scholarship.scores.index');
        $update = route('admin.scholarship.scores.update', $application);
        $this->actingAs($this->admin)->from($index)->patch($update, ['score' => 75])
            ->assertRedirect()->assertSessionHasNoErrors();
        $fresh = $application->fresh();
        self::assertSame([75, 40, true, 'unreached', 'reached'], [
            $fresh->score, $fresh->scholarship_percentage, $fresh->result_published,
            $fresh->result_contact_status, $fresh->application_contact_status,
        ]);
        self::assertSame(75, $this->applications->forMember($this->student, $application->id)['result']['score']);
        $this->patch($update, ['score' => '', 'correct_count' => '0', 'wrong_count' => '', 'blank_count' => '2'])->assertRedirect()->assertSessionHasNoErrors();
        self::assertNull($application->fresh()->score);
        self::assertSame([0, null, 2], [$application->fresh()->correct_count, $application->fresh()->wrong_count, $application->fresh()->blank_count]);
        self::assertTrue($application->fresh()->result_published);

        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $this->flushSession();
        $this->patch($update, ['score' => '', 'correct_count' => '0', 'wrong_count' => '', 'blank_count' => '2'])->assertRedirect()->assertSessionHasNoErrors();
        self::assertSame('reached', $application->fresh()->result_contact_status);
    }
}
