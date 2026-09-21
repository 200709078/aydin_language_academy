<?php

namespace Tests\Scholarship;

use App\Models\ScholarshipApplication;
use App\Models\ScholarshipNotification;
use DOMDocument;
use DOMXPath;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ViewErrorBag;

class ScholarshipExamCatalogTest extends ScholarshipTestCase
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

        // The real shared header reads these tables. Keep them empty and isolated.
        if (! Schema::hasTable('themes')) {
            Schema::create('themes', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('level_id')->nullable();
                $table->unsignedBigInteger('sub_level_id')->nullable();
            });
        }
        foreach (['levels', 'sub_levels'] as $name) {
            if (! Schema::hasTable($name)) {
                Schema::create($name, function (Blueprint $table): void {
                    $table->id();
                    $table->string('name');
                    $table->string('slug');
                });
            }
        }
    }

    protected function tearDown(): void
    {
        $this->app['auth']->forgetGuards();
        $this->flushSession();
        $this->app->setLocale($this->originalLocale);
        $this->app['view']->share('errors', new ViewErrorBag);
        parent::tearDown();
    }

    public function test_catalog_requires_login_and_only_exposes_availability_and_own_application_presence(): void
    {
        $url = route('frontend.scholarship.exams.index');
        $this->get($url)->assertRedirect(route('login'));
        $this->period->update(['title' => 'Period <script>alert(1)</script>']);
        $this->other->update(['name' => 'Private applicant']);
        $application = $this->applications->create($this->other, $this->data());
        $this->applications->updateResult($this->admin, $application->id, ['score' => 73, 'scholarship_percentage' => 90]);

        $page = $this->actingAs($this->student)->get($url)->assertOk()
            ->assertSee($this->period->title)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee(__('scholarship.member_session_available'))
            ->assertSee(__('scholarship.member_remaining_seats', ['count' => 1]))
            ->assertDontSee('Private applicant')
            ->assertDontSee($this->other->email)
            ->assertDontSee($application->application_number)
            ->assertDontSee(__('scholarship.member_existing_application'));
        self::assertSame(2, substr_count($page->getContent(), 'href="'.$url.'"'));
        $period = $page->viewData('periods')->first();
        self::assertFalse($period['has_application']);
        self::assertSame([1, 2], array_column($period['sessions'], 'remaining'));
        self::assertStringNotContainsString('scholarship_percentage', json_encode($period));
        self::assertStringNotContainsString('score', json_encode($period));

        $this->applications->create($this->student, $this->data($this->alternative));
        $ownPage = $this->get($url)->assertOk()->assertSee(__('scholarship.member_existing_application'));
        self::assertSame(['applied', 'applied'], array_column($ownPage->viewData('periods')->first()['sessions'], 'state'));
        self::assertSame(2, ScholarshipApplication::query()->count());
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    public function test_application_list_requires_login_and_keeps_current_and_archived_history_scoped_to_the_account(): void
    {
        $url = route('frontend.scholarship.applications.index');
        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs($this->student)->get($url)->assertOk()
            ->assertSee(__('scholarship.member_applications_empty'));

        $current = $this->applications->create($this->student, $this->data());
        $this->other->update(['name' => 'Private application owner']);
        $foreign = $this->applications->create($this->other, $this->data($this->alternative));
        $period = $this->period->replicate();
        $period->title = 'Previous period <script>alert(1)</script>';
        $period->save();
        $session = $this->session->replicate();
        $session->period_id = $period->id;
        $session->save();
        $historical = $this->applications->create($this->student, $this->data($session));
        $period->update([
            'is_active' => false, 'applications_open' => false,
            'applications_open_at' => '2034-01-01', 'applications_close_at' => '2034-01-20',
            'exam_starts_on' => '2034-01-25', 'exam_ends_on' => '2034-01-26',
        ]);
        $session->update(['exam_date' => '2034-01-25', 'archived_at' => now()]);
        $this->session->update(['is_active' => false]);

        $page = $this->get($url.'?user_id='.$this->other->id)->assertOk()
            ->assertSee($current->application_number)->assertSee($historical->application_number)
            ->assertSee($period->title)->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee($this->student->name)->assertSee($this->branch->name)->assertSee($this->group->name)
            ->assertSee($this->session->exam_title)->assertSee('25.01.2034')->assertSee('08:00–10:00')
            ->assertDontSee($foreign->application_number)->assertDontSee('Private application owner')->assertDontSee($this->other->email);
        $applications = $page->viewData('applications');
        self::assertSame(2, $applications->total());
        self::assertSame(10, $applications->perPage());
        self::assertSame([$historical->id, $current->id], $applications->pluck('id')->all());
        self::assertSame(2, substr_count($page->getContent(), 'href="'.$url.'"'));
        $this->get(route('frontend.scholarship.exams.index'))->assertOk()->assertSee('href="'.$url.'"', false);

        // Admin accounts also see only their own records in the member area.
        $this->actingAs($this->admin)->get($url.'?user_id='.$this->student->id)->assertOk()
            ->assertSee(__('scholarship.member_applications_empty'))
            ->assertDontSee($current->application_number)->assertDontSee($historical->application_number);
    }

    public function test_application_list_separates_approval_and_result_publication_without_exposing_private_fields(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $url = route('frontend.scholarship.applications.index');
        $this->actingAs($this->student);
        $pending = $this->get($url)->assertOk()->viewData('applications')->first();
        $this->applications->approve($this->admin, $application->id);
        $this->applications->updateResult($this->admin, $application->id, [
            'score' => 73, 'scholarship_percentage' => 90,
            'correct_count' => 60001, 'wrong_count' => 60002, 'blank_count' => 60003,
        ]);
        $unpublished = $this->get($url)->assertOk()
            ->assertSee(__('scholarship.member_application_under_review'))
            ->assertSee(__('scholarship.member_application_unpublished'))
            ->assertSee(__('scholarship.member_result_unpublished'))
            ->assertDontSee(__('scholarship.delivery_accepted'))
            ->assertDontSee(__('scholarship.delivery_arrive_early'))
            ->assertDontSee('60001')->assertDontSee('60002')->assertDontSee('60003');
        $data = $unpublished->viewData('applications')->first();
        self::assertSame($pending, $data);
        foreach (['score', 'scholarship_percentage', 'correct_count', 'wrong_count', 'blank_count',
            'application_contact_status', 'result_contact_status', 'status', 'user_id', 'can_edit', 'restriction'] as $field) {
            self::assertStringNotContainsString('"'.$field.'"', json_encode($data));
        }

        $this->applications->publish($this->admin, [$application->id], 'result', true);
        $resultOnly = $this->get($url)->assertOk()->assertSee(__('scholarship.member_result_published'))
            ->assertDontSee(__('scholarship.delivery_accepted'));
        self::assertSame(['published' => true], $resultOnly->viewData('applications')->first()['result']);

        $this->applications->publish($this->admin, [$application->id], 'application', true);
        $this->get($url)->assertOk()->assertSee(__('scholarship.delivery_accepted'))
            ->assertSee(__('scholarship.delivery_arrive_early'))->assertSee(__('scholarship.member_result_published'))
            ->assertDontSee(__('scholarship.member_application_unpublished'));
        $this->applications->publish($this->admin, [$application->id], 'application', false);
        $this->get($url)->assertOk()->assertSee(__('scholarship.member_result_published'))
            ->assertDontSee(__('scholarship.delivery_accepted'));
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    public function test_application_detail_requires_ownership_and_keeps_unpublished_approval_and_results_private(): void
    {
        $this->school->update(['name' => 'School <script>alert(1)</script>']);
        $application = $this->applications->create($this->student, $this->data());
        $url = route('frontend.scholarship.applications.show', $application->id);
        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs($this->other)->get($url.'?user_id='.$this->student->id)->assertForbidden()
            ->assertDontSee($application->application_number);
        $this->actingAs($this->admin)->get($url)->assertForbidden();
        $this->actingAs($this->student)->get(route('frontend.scholarship.applications.show', 99999999))->assertNotFound();
        $this->applications->updateResult($this->admin, $application->id, [
            'score' => 73, 'scholarship_percentage' => 90,
            'correct_count' => 60001, 'wrong_count' => 60002, 'blank_count' => 60003,
        ]);
        $this->student->update(['name' => 'Updated profile name']);
        $this->school->update(['name' => 'Renamed school']);
        $pending = $this->get($url)->assertOk()->assertSee($application->application_number)
            ->assertSee($application->student_name_snapshot)->assertSee($application->school_name_snapshot)
            ->assertSee($application->student_level_name_snapshot)->assertSee($this->group->name)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->viewData('application');
        $this->applications->approve($this->admin, $application->id);
        $page = $this->get($url)->assertOk()->assertSee(__('scholarship.member_application_under_review'))
            ->assertSee(__('scholarship.member_application_unpublished'))
            ->assertSee(__('scholarship.member_result_unpublished'))
            ->assertDontSee(__('scholarship.delivery_accepted'))->assertDontSee(__('scholarship.member_restriction_read_only'))
            ->assertDontSee(__('scholarship.member_arrival_deadline'))
            ->assertDontSee('60001')->assertDontSee('60002')->assertDontSee('60003');
        self::assertSame($pending, $page->viewData('application'));
        foreach (['score', 'scholarship_percentage', 'correct_count', 'wrong_count', 'blank_count',
            'attendance_status', 'application_contact_status', 'result_contact_status', 'user_id', 'can_edit', 'can_delete'] as $field) {
            self::assertStringNotContainsString('"'.$field.'"', json_encode($page->viewData('application')));
        }
        $this->get(route('frontend.scholarship.applications.index'))->assertOk()->assertSee('href="'.$url.'"', false);
    }

    public function test_application_detail_shows_published_changes_and_session_or_period_restrictions_without_write_actions(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $url = route('frontend.scholarship.applications.show', $application->id);
        $this->applications->approve($this->admin, $application->id);
        $this->applications->publish($this->admin, [$application->id], 'application');
        $this->actingAs($this->student)->get($url)->assertOk()->assertSee(__('scholarship.delivery_accepted'))
            ->assertSee(__('scholarship.delivery_arrive_early'))->assertSee('25.01.2035 07:30')
            ->assertSee(__('scholarship.member_restriction_read_only'))
            ->assertSee(__('scholarship.member_result_unpublished'));

        $this->applications->updateResult($this->admin, $application->id, ['scholarship_percentage' => 0]);
        $this->applications->publish($this->admin, [$application->id], 'result');
        $this->applications->update($this->admin, $application->id, ['session_id' => $this->alternative->id]);
        $page = $this->get($url)->assertOk()->assertSee($this->otherBranch->name)
            ->assertSee('16:00–18:00')->assertSee('25.01.2035 15:30')->assertDontSee('25.01.2035 07:30')
            ->assertSee(__('scholarship.member_result_published'))->assertDontSee(__('scholarship.application_edit'));
        self::assertSame(['published' => true], $page->viewData('application')['result']);
        $dom = new DOMDocument;
        $dom->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        self::assertCount(0, (new DOMXPath($dom))->query('//main//form'));

        foreach ([
            ['changes' => ['is_active' => false], 'restriction' => 'suspended'],
            ['changes' => ['archived_at' => now()], 'restriction' => 'archived'],
        ] as $case) {
            $this->alternative->update($case['changes']);
            $this->get($url)->assertOk()->assertSee(__('scholarship.member_restriction_'.$case['restriction']))
                ->assertSee('href="'.route('frontend.contact').'"', false)->assertSee($application->application_number);
        }
        $this->alternative->update(['is_active' => true, 'archived_at' => null]);
        $this->period->update(['applications_open' => false]);
        $this->get($url)->assertOk()->assertSee(__('scholarship.member_restriction_applications_closed'));
        $this->applications->publish($this->admin, [$application->id], 'application', false);
        $this->get($url)->assertOk()->assertSee(__('scholarship.member_application_unpublished'))
            ->assertSee(__('scholarship.member_result_published'))->assertDontSee(__('scholarship.delivery_accepted'))
            ->assertDontSee(__('scholarship.member_arrival_deadline'));
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    public function test_period_switch_and_application_window_control_displayed_availability(): void
    {
        $this->actingAs($this->student);
        $url = route('frontend.scholarship.exams.index');
        foreach ([
            ['applications_open' => false, 'applications_open_at' => '2035-01-01', 'applications_close_at' => '2035-01-20', 'state' => 'closed'],
            ['applications_open' => true, 'applications_open_at' => '2035-01-11', 'applications_close_at' => '2035-01-20', 'state' => 'upcoming'],
            ['applications_open' => true, 'applications_open_at' => '2035-01-01', 'applications_close_at' => '2035-01-09', 'state' => 'ended'],
        ] as $case) {
            $state = $case['state'];
            unset($case['state']);
            $this->period->update($case);
            $page = $this->get($url)->assertOk()->assertSee(__('scholarship.member_period_'.$state))
                ->assertDontSee(__('scholarship.member_session_available'));
            self::assertSame(['closed', 'closed'], array_column($page->viewData('periods')->first()['sessions'], 'state'));
        }

        $this->period->update(['is_active' => false]);
        $this->get($url)->assertOk()->assertSee(__('scholarship.member_exams_empty'))->assertDontSee($this->period->title);
    }

    public function test_session_capacity_suspension_and_exclusion_of_unavailable_definitions_and_dates(): void
    {
        $this->applications->create($this->other, $this->data());
        $approved = $this->applications->create($this->third, $this->data());
        $this->applications->approve($this->admin, $approved->id);
        $this->alternative->update(['is_active' => false]);
        $url = route('frontend.scholarship.exams.index');
        $page = $this->actingAs($this->student)->get($url)->assertOk()
            ->assertSee(__('scholarship.session_full'))->assertSee(__('scholarship.member_suspended_help'))
            ->assertSee('href="'.route('frontend.contact').'"', false);
        self::assertSame(['full', 'suspended'], array_column($page->viewData('periods')->first()['sessions'], 'state'));

        $this->session->update(['archived_at' => now()]);
        // At the exact start time the remaining session is no longer listed.
        $this->alternative->update(['is_active' => true, 'exam_date' => '2035-01-10', 'starts_at' => '12:00:00', 'ends_at' => '14:00:00']);
        $this->get($url)->assertOk()->assertSee(__('scholarship.member_exams_empty'));
        $this->alternative->update(['exam_date' => '2035-01-09']);
        $this->get($url)->assertOk()->assertSee(__('scholarship.member_exams_empty'));
        $this->alternative->update(['exam_date' => '2035-01-25']);
        $this->otherBranch->update(['is_active' => false]);
        $this->get($url)->assertOk()->assertSee(__('scholarship.member_exams_empty'));
        $this->otherBranch->update(['is_active' => true]);
        $this->group->update(['is_active' => false]);
        $this->get($url)->assertOk()->assertSee(__('scholarship.member_exams_empty'));
    }

    public function test_form_uses_the_signed_in_account_and_independent_school_level_and_exam_choices_without_writing(): void
    {
        $url = route('frontend.scholarship.applications.create', ['period' => $this->period->id, 'session' => $this->session->id]);
        $this->get($url)->assertRedirect(route('login'));
        $this->student->update(['phone' => null]);
        $this->catalog->saveDefinition($this->admin, 'school', ['name' => 'Hidden school', 'is_active' => false]);
        $this->catalog->saveDefinition($this->admin, 'student_level', ['code' => 'hidden', 'name' => 'Hidden grade', 'is_active' => false]);
        $page = $this->actingAs($this->student)->get($url.'&user_id='.$this->other->id)->assertOk()
            ->assertSee(__('scholarship.member_form_title'))
            ->assertSee($this->student->email)->assertDontSee($this->other->email)
            ->assertSee(__('scholarship.member_contact_missing'))->assertSee(route('profile.show'))
            ->assertSee('1. Sınıf')->assertSee('Lise')->assertSee($this->school->name)
            ->assertDontSee('Hidden school')->assertDontSee('Hidden grade');
        $dom = new DOMDocument;
        $dom->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        self::assertCount(3, $xpath->query('//input[@readonly and (@id="student_name" or @id="account_email" or @id="account_phone") and not(@name)]'));
        self::assertCount(6, $xpath->query('//form[@id="scholarship-application-form"]//select[@required]'));
        self::assertCount(1, $xpath->query('//select[@name="session_id"]/option[@selected and not(@disabled) and @value="'.$this->session->id.'"]'));
        self::assertCount(1, $xpath->query('//form[@id="scholarship-application-form"]//button[@type="submit" and not(@disabled)]'));
        self::assertSame('2', $xpath->query('//input[@id="chosen_remaining"]')->item(0)->getAttribute('value'));
        $this->get(route('frontend.scholarship.exams.index'))->assertOk()->assertSee('href="'.$url.'"', false);
        self::assertCount(0, $xpath->query('//form[@id="scholarship-application-form" and @onsubmit]'));
        self::assertSame(route('frontend.scholarship.applications.store', $this->period->id), $dom->getElementById('scholarship-application-form')->getAttribute('action'));
        self::assertSame(0, ScholarshipApplication::query()->count());
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    public function test_submission_uses_only_the_account_and_route_reserves_a_seat_and_blocks_duplicates(): void
    {
        $store = route('frontend.scholarship.applications.store', $this->period->id);
        $index = route('frontend.scholarship.exams.index');
        $this->post($store, $this->data())->assertRedirect(route('login'));
        $this->actingAs($this->student)->post($store, [
            ...$this->data(), 'user_id' => $this->other->id, 'student_name' => 'Forged name', 'period_id' => 999999,
            'branch_id' => $this->otherBranch->id, 'exam_group_id' => 999999, 'exam_title' => 'Forged exam',
            'status' => 'approved', 'application_published' => true, 'result_published' => true,
            'score' => 100, 'scholarship_percentage' => 100, 'correct_count' => 50,
        ])->assertRedirect($index)->assertSessionHasNoErrors();
        $application = ScholarshipApplication::query()->sole();
        self::assertSame([$this->student->id, $this->student->name, $this->period->id, $this->session->id, 'pending'], [
            $application->user_id, $application->student_name_snapshot, $application->period_id, $application->session_id, $application->status,
        ]);
        self::assertSame([false, false, null, null, null], [$application->application_published, $application->result_published,
            $application->score, $application->scholarship_percentage, $application->correct_count]);
        self::assertSame(1, $this->session->applications()->count());
        self::assertSame($application->application_number, session('scholarship_application_created.number'));
        $this->get($index)->assertOk()->assertSee(__('scholarship.member_application_created', ['number' => $application->application_number]));
        $this->post($store, $this->data($this->alternative))->assertSessionHasErrors('period_id');
        self::assertSame(1, ScholarshipApplication::query()->count());
        self::assertSame(0, $this->alternative->applications()->count());

        $this->flushSession();
        $this->actingAs($this->other)->post($store, $this->data($this->alternative))->assertRedirect($index)->assertSessionHasNoErrors();
        self::assertNotSame($application->application_number, ScholarshipApplication::query()->where('user_id', $this->other->id)->sole()->application_number);
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    public function test_submission_errors_preserve_selections_and_reject_invalid_definitions_and_cross_period_sessions(): void
    {
        $store = route('frontend.scholarship.applications.store', $this->period->id);
        $form = route('frontend.scholarship.applications.create', $this->period->id);
        $this->actingAs($this->student)->post($store, ['session_id' => (string) $this->session->id, 'student_level_id' => (string) $this->level->id])
            ->assertRedirect($form)->assertSessionHasErrors('school_id');
        $page = $this->get($form)->assertOk();
        self::assertSame($this->session->id, $page->viewData('selectedSession')['id']);
        $dom = new DOMDocument;
        $dom->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        self::assertCount(1, (new DOMXPath($dom))->query('//select[@name="student_level_id"]/option[@selected and @value="'.$this->level->id.'"]'));
        $this->post($store, [...$this->data(), 'session_id' => ['bad']])->assertRedirect($form)->assertSessionHasErrors('session_id');
        $this->get($form)->assertOk();
        $this->school->update(['is_active' => false]);
        $this->post($store, $this->data())->assertSessionHasErrors('school_id');
        $this->school->update(['is_active' => true]);
        $this->level->update(['is_active' => false]);
        $this->post($store, $this->data())->assertSessionHasErrors('student_level_id');
        $this->level->update(['is_active' => true]);
        $foreignPeriod = $this->period->replicate();
        $foreignPeriod->save();
        $foreignSession = $this->session->replicate();
        $foreignSession->period_id = $foreignPeriod->id;
        $foreignSession->save();
        $this->post($store, $this->data($foreignSession))->assertSessionHasErrors('session_id');
        self::assertSame(0, ScholarshipApplication::query()->count());
    }

    public function test_submission_rechecks_period_session_and_capacity_after_the_form_was_opened(): void
    {
        $store = route('frontend.scholarship.applications.store', $this->period->id);
        $form = route('frontend.scholarship.applications.create', ['period' => $this->period->id, 'session' => $this->session->id]);
        $this->actingAs($this->student)->get($form)->assertOk();
        $this->period->update(['applications_open' => false]);
        $this->post($store, $this->data())->assertSessionHasErrors('period_id');
        $this->period->update(['applications_open' => true, 'applications_close_at' => '2035-01-09 23:59:59']);
        $this->post($store, $this->data())->assertSessionHasErrors('period_id');
        $this->period->update(['applications_close_at' => '2035-01-20 23:59:59']);
        $this->session->update(['is_active' => false]);
        $this->post($store, $this->data())->assertSessionHasErrors('session_id');
        $this->session->update(['is_active' => true, 'archived_at' => now()]);
        $this->post($store, $this->data())->assertSessionHasErrors('session_id');
        $this->session->update(['archived_at' => null, 'capacity' => 1]);
        $this->applications->create($this->other, $this->data());
        $this->post($store, $this->data())->assertSessionHasErrors('session_id');
        self::assertSame(1, $this->session->applications()->count());
        self::assertSame(0, $this->student->scholarshipApplications()->count());
        $this->flushSession();
        $this->post($store, $this->data($this->alternative))->assertSessionHasNoErrors();
        self::assertSame(1, $this->alternative->applications()->count());
    }

    public function test_form_never_preselects_unavailable_sessions_and_blocks_closed_or_duplicate_periods(): void
    {
        $this->applications->create($this->other, $this->data());
        $this->applications->create($this->third, $this->data());
        $this->alternative->update(['is_active' => false]);
        $open = $this->session->replicate();
        $open->fill(['starts_at' => '10:00:00', 'ends_at' => '12:00:00'])->save();
        $archived = $this->session->replicate();
        $archived->fill(['starts_at' => '12:00:00', 'ends_at' => '14:00:00', 'archived_at' => now()])->save();
        $url = route('frontend.scholarship.applications.create', $this->period->id);
        $index = route('frontend.scholarship.exams.index');
        $this->actingAs($this->student);
        foreach ([$this->session, $this->alternative, $archived] as $unavailable) {
            $page = $this->get($url.'?session='.$unavailable->id)->assertOk()->assertSee(__('scholarship.member_selection_unavailable'));
            self::assertNull($page->viewData('selectedSession'));
        }
        $page = $this->get($url.'?session='.$open->id)->assertOk();
        $dom = new DOMDocument;
        $dom->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        self::assertCount(2, $xpath->query('//select[@name="session_id"]/option[@disabled and (@data-state="full" or @data-state="suspended")]'));
        self::assertCount(0, $xpath->query('//select[@name="session_id"]/option[@value="'.$archived->id.'"]'));
        self::assertCount(1, $xpath->query('//select[@name="session_id"]/option[@selected and not(@disabled) and @value="'.$open->id.'"]'));
        $open->update(['is_active' => false]);
        $this->get($url)->assertRedirect($index)->assertSessionHas('scholarship_notice', __('scholarship.member_no_available_sessions'));
        $open->update(['is_active' => true]);
        $this->period->update(['applications_open' => false]);
        $this->get($url)->assertRedirect($index)->assertSessionHas('scholarship_notice', __('scholarship.period_not_accepting'));
        $this->period->update(['applications_open' => true]);
        $this->applications->create($this->student, $this->data($open));
        $this->get($url)->assertRedirect($index)->assertSessionHas('scholarship_notice', __('scholarship.member_existing_application'));
    }
}
