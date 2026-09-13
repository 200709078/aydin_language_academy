<?php

namespace Tests\Scholarship;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ViewErrorBag;

class ScholarshipAttendanceAdminTest extends ScholarshipTestCase
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

    public function test_attendance_routes_require_admin_authentication(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $index = route('admin.scholarship.attendance.index');
        $update = route('admin.scholarship.attendance.update', $application);
        $this->get($index)->assertRedirect(route('login'));
        $this->patch($update, ['attendance_status' => 'absent'])->assertRedirect(route('login'));
        $this->actingAs($this->student)->get($index)->assertForbidden();
        $this->patch($update, ['attendance_status' => 'absent'])->assertForbidden();
        self::assertSame('unmarked', $application->fresh()->attendance_status);
    }

    public function test_filtered_page_and_row_save_preserve_context_and_only_change_attendance(): void
    {
        Mail::fake();
        Notification::fake();
        $application = $this->applications->create($this->student, $this->data());
        $other = $this->applications->create($this->other, $this->data($this->alternative));
        $filters = ['period_id' => $this->period->id, 'branch_id' => $this->branch->id,
            'exam_group_id' => $this->group->id, 'student_level_id' => $this->level->id,
            'exam_date' => '2035-01-25', 'starts_at' => '08:00', 'ends_at' => '10:00', 'page' => 1];
        $index = route('admin.scholarship.attendance.index', $filters);
        $update = route('admin.scholarship.attendance.update', ['application' => $application->id, ...$filters]);

        $page = $this->actingAs($this->admin)->get($index)->assertOk()
            ->assertSee(__('scholarship.attendance_management'))
            ->assertSee($application->application_number)->assertDontSee($other->application_number)
            ->assertSee('1. Sınıf')->assertSee('Lise')
            ->assertSee('ala-action-confirmation', false);
        self::assertEquals($filters, $page->viewData('filters'));
        $page->assertSee(route('admin.scholarship.attendance.update', ['application' => $application->id, ...$page->viewData('filters')]));
        $this->from($index)->patch($update, ['attendance_status' => 'attended', 'score' => 99,
            'scholarship_percentage' => 100, 'result_published' => true, 'status' => 'approved',
            'user_id' => $this->other->id, 'session_id' => $this->alternative->id])
            ->assertRedirect(route('admin.scholarship.attendance.index', $page->viewData('filters')).'#attendance-row-'.$application->id)->assertSessionHasNoErrors()
            ->assertSessionHas('modalSuccessContent');
        $fresh = $application->fresh();
        self::assertSame('attended', $fresh->attendance_status);
        self::assertNull($fresh->score);
        self::assertNull($fresh->scholarship_percentage);
        self::assertFalse($fresh->result_published);
        self::assertSame('pending', $fresh->status);
        self::assertSame($this->student->id, $fresh->user_id);
        self::assertSame($this->session->id, $fresh->session_id);
        self::assertSame('unmarked', $other->fresh()->attendance_status);
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
        Notification::assertNothingSent();
    }

    public function test_absence_updates_results_and_contact_but_cannot_leave_incomplete_published_results(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->updateResult($this->admin, $application->id, [
            'attendance_status' => 'attended', 'score' => 75, 'scholarship_percentage' => 50,
        ]);
        $this->applications->publish($this->admin, [$application->id], 'result');
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $index = route('admin.scholarship.attendance.index');
        $update = route('admin.scholarship.attendance.update', $application);
        $this->actingAs($this->admin)->from($index)->patch($update, ['attendance_status' => 'absent'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $fresh = $application->fresh();
        self::assertSame(['absent', 0, 0, true, 'unreached', 'reached'], [
            $fresh->attendance_status, $fresh->score, $fresh->scholarship_percentage,
            $fresh->result_published, $fresh->result_contact_status, $fresh->application_contact_status,
        ]);
        $this->patch($update, ['attendance_status' => 'attended'])
            ->assertRedirect($index)->assertSessionHasErrors('result_published');
        self::assertSame('absent', $application->fresh()->attendance_status);
        self::assertSame(0, $application->fresh()->score);

        $this->applications->publish($this->admin, [$application->id], 'result', false);
        $this->flushSession();
        $this->patch($update, ['attendance_status' => 'attended'])->assertRedirect()->assertSessionHasNoErrors();
        self::assertNull($application->fresh()->score);
        self::assertNull($application->fresh()->scholarship_percentage);
        $this->patch($update, ['attendance_status' => 'unmarked'])->assertRedirect()->assertSessionHasNoErrors();
        self::assertSame('unmarked', $application->fresh()->attendance_status);
        self::assertNull($application->fresh()->score);
    }

    public function test_missing_or_invalid_status_cannot_use_query_filter_and_unchanged_status_keeps_contact(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $index = route('admin.scholarship.attendance.index');
        $update = route('admin.scholarship.attendance.update', ['application' => $application->id, 'attendance_status' => 'absent']);
        $this->actingAs($this->admin)->from($index)->patch($update, [])->assertSessionHasErrors('attendance_status');
        $this->patch($update, ['attendance_status' => 'invalid'])->assertSessionHasErrors('attendance_status');
        self::assertSame('unmarked', $application->fresh()->attendance_status);
        $this->flushSession();
        $this->patch($update, ['attendance_status' => 'unmarked'])->assertRedirect()->assertSessionHasNoErrors();
        self::assertSame('reached', $application->fresh()->result_contact_status);
        self::assertNull($application->fresh()->score);
    }
}
