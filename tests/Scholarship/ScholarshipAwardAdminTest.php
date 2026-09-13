<?php

namespace Tests\Scholarship;

use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ViewErrorBag;

class ScholarshipAwardAdminTest extends ScholarshipTestCase
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

    public function test_award_routes_require_admin_authentication(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $index = route('admin.scholarship.awards.index');
        $update = route('admin.scholarship.awards.update', $application);
        $this->get($index)->assertRedirect(route('login'));
        $this->patch($update, ['scholarship_percentage' => 90])->assertRedirect(route('login'));
        $this->actingAs($this->student)->get($index)->assertForbidden();
        $this->patch($update, ['scholarship_percentage' => 90])->assertForbidden();
        self::assertNull($application->fresh()->scholarship_percentage);
    }

    public function test_award_list_sorts_by_score_and_distinguishes_unset_and_zero_selections(): void
    {
        $high = $this->applications->create($this->student, $this->data());
        $zero = $this->applications->create($this->other, $this->data());
        $missing = $this->applications->create($this->third, $this->data($this->alternative));
        $this->applications->updateResult($this->admin, $high->id, ['score' => 100]);
        $this->applications->updateResult($this->admin, $zero->id, ['score' => 50, 'scholarship_percentage' => 0]);
        $index = route('admin.scholarship.awards.index');
        $page = $this->actingAs($this->admin)->get($index)->assertOk()
            ->assertSee(__('scholarship.award_management'))->assertSee(__('scholarship.award_unset'))
            ->assertSee(__('scholarship.award_none'))->assertSee('action="'.$index.'"', false);
        self::assertSame([$high->id, $zero->id, $missing->id], $page->viewData('applications')->pluck('id')->all());
        $dom = new DOMDocument;
        $dom->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        foreach ([$high->id => '', $zero->id => '0'] as $id => $value) {
            $radios = $xpath->query('//form[@id="award-'.$id.'"]//input[@type="radio"]');
            self::assertCount(12, $radios);
            $checked = $xpath->query('//form[@id="award-'.$id.'"]//input[@type="radio" and @checked]');
            self::assertCount(1, $checked);
            self::assertSame($value, $checked->item(0)->getAttribute('value'));
        }
        $zeroPage = $this->get(route('admin.scholarship.awards.index', [
            'branch_id' => $this->branch->id, 'exam_group_id' => $this->group->id,
            'student_level_id' => $this->level->id, 'scholarship_percentage' => 0,
        ]))->assertOk();
        self::assertSame([$zero->id], $zeroPage->viewData('applications')->pluck('id')->all());
        $unset = $this->get(route('admin.scholarship.awards.index', ['scholarship_percentage' => 'unset']))->assertOk();
        self::assertSame([$high->id, $missing->id], $unset->viewData('applications')->pluck('id')->all());
    }

    public function test_row_save_changes_only_this_period_award_and_preserves_zero_and_null(): void
    {
        Mail::fake();
        Notification::fake();
        $application = $this->applications->create($this->student, $this->data());
        $otherPeriod = $this->catalog->savePeriod($this->admin, [
            'title' => 'Another exam period', 'applications_open_at' => '2035-01-01 00:00:00',
            'applications_close_at' => '2035-01-20 23:59:59', 'exam_starts_on' => '2035-01-26',
            'exam_ends_on' => '2035-01-26', 'is_active' => true, 'applications_open' => true,
        ]);
        $otherSession = $this->catalog->saveSession($this->admin, [
            'period_id' => $otherPeriod->id, 'branch_id' => $this->branch->id, 'exam_group_id' => $this->group->id,
            'exam_title' => 'Another exam', 'exam_date' => '2035-01-26', 'starts_at' => '08:00:00', 'ends_at' => '10:00:00', 'capacity' => 2,
        ]);
        $otherApplication = $this->applications->create($this->student, $this->data($otherSession));
        $this->applications->updateResult($this->admin, $otherApplication->id, ['scholarship_percentage' => 70]);
        $filters = ['sort' => 'name', 'period_id' => $this->period->id];
        $update = route('admin.scholarship.awards.update', ['application' => $application->id, ...$filters]);
        $this->actingAs($this->admin)->patch($update, ['scholarship_percentage' => '100', 'score' => 99,
            'attendance_status' => 'absent', 'result_published' => true, 'status' => 'approved',
            'user_id' => $this->other->id, 'session_id' => $otherSession->id, 'period_id' => $otherPeriod->id])
            ->assertRedirect(route('admin.scholarship.awards.index', $filters).'#award-row-'.$application->id)
            ->assertSessionHasNoErrors()->assertSessionHas('modalSuccessContent');
        $fresh = $application->fresh();
        self::assertSame([100, null, 'unmarked', false, 'pending', $this->student->id, $this->period->id, $this->session->id], [
            $fresh->scholarship_percentage, $fresh->score, $fresh->attendance_status, $fresh->result_published,
            $fresh->status, $fresh->user_id, $fresh->period_id, $fresh->session_id,
        ]);
        self::assertSame(['published' => false], $this->applications->forMember($this->student, $application->id)['result']);
        $this->patch($update, ['scholarship_percentage' => '0'])->assertRedirect()->assertSessionHasNoErrors();
        self::assertSame(0, $application->fresh()->scholarship_percentage);
        $this->patch($update, ['scholarship_percentage' => ''])->assertRedirect()->assertSessionHasNoErrors();
        self::assertNull($application->fresh()->scholarship_percentage);
        self::assertSame(70, $otherApplication->fresh()->scholarship_percentage);
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
        Notification::assertNothingSent();
    }

    public function test_invalid_missing_and_absent_student_awards_are_rejected(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $update = route('admin.scholarship.awards.update', $application);
        $this->actingAs($this->admin)->from(route('admin.scholarship.awards.index'));
        foreach ([55, '50.5', 110] as $invalid) {
            $this->patch($update, ['scholarship_percentage' => $invalid])->assertSessionHasErrors('scholarship_percentage');
        }
        $this->patch($update.'?scholarship_percentage=0', [])->assertSessionHasErrors('scholarship_percentage');
        self::assertNull($application->fresh()->scholarship_percentage);
        $this->applications->updateResult($this->admin, $application->id, ['attendance_status' => 'absent']);
        $this->patch($update, ['scholarship_percentage' => 30, 'attendance_status' => 'attended'])
            ->assertSessionHasErrors('scholarship_percentage');
        $fresh = $application->fresh();
        self::assertSame(['absent', 0, 0], [$fresh->attendance_status, $fresh->score, $fresh->scholarship_percentage]);
    }

    public function test_published_award_updates_visibility_and_contact_but_cannot_be_unset(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->updateResult($this->admin, $application->id, [
            'attendance_status' => 'attended', 'score' => 80, 'scholarship_percentage' => 40,
        ]);
        $this->applications->publish($this->admin, [$application->id], 'result');
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $index = route('admin.scholarship.awards.index');
        $update = route('admin.scholarship.awards.update', $application);
        $this->actingAs($this->admin)->from($index)->patch($update, ['scholarship_percentage' => 90])
            ->assertRedirect()->assertSessionHasNoErrors();
        $fresh = $application->fresh();
        self::assertSame([90, 80, true, 'unreached', 'reached'], [
            $fresh->scholarship_percentage, $fresh->score, $fresh->result_published,
            $fresh->result_contact_status, $fresh->application_contact_status,
        ]);
        self::assertSame(90, $this->applications->forMember($this->student, $application->id)['result']['scholarship_percentage']);
        $this->patch($update, ['scholarship_percentage' => ''])->assertRedirect($index)->assertSessionHasErrors('result_published');
        self::assertSame(90, $application->fresh()->scholarship_percentage);
        self::assertTrue($application->fresh()->result_published);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $this->flushSession();
        $this->patch($update, ['scholarship_percentage' => 90])->assertRedirect()->assertSessionHasNoErrors();
        self::assertSame('reached', $application->fresh()->result_contact_status);
    }
}
