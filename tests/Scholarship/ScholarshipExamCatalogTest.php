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
        self::assertCount(1, $xpath->query('//form[@id="scholarship-application-form"]//button[@type="submit" and @disabled]'));
        self::assertSame('2', $xpath->query('//input[@id="chosen_remaining"]')->item(0)->getAttribute('value'));
        $this->get(route('frontend.scholarship.exams.index'))->assertOk()->assertSee('href="'.$url.'"', false);
        $this->post(route('frontend.scholarship.applications.create', $this->period->id), $this->data())->assertStatus(405);
        self::assertSame(0, ScholarshipApplication::query()->count());
        self::assertSame(0, ScholarshipNotification::query()->count());
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
