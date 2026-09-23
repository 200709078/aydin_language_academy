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
        self::assertSame(['published' => true, 'attendance_status' => 'unmarked', 'score' => 73,
            'correct_count' => 60001, 'wrong_count' => 60002, 'blank_count' => 60003, 'scholarship_percentage' => 90],
            $resultOnly->viewData('applications')->first()['result']);

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
        $pending['can_edit'] = $pending['can_delete'] = false;
        self::assertSame($pending, $page->viewData('application'));
        $page->assertDontSee(__('scholarship.application_edit'))->assertDontSee('data-confirm-form="member-application-delete"', false);
        foreach (['score', 'scholarship_percentage', 'correct_count', 'wrong_count', 'blank_count',
            'attendance_status', 'application_contact_status', 'result_contact_status', 'user_id'] as $field) {
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
        self::assertSame(['published' => true, 'attendance_status' => 'unmarked', 'score' => null,
            'correct_count' => null, 'wrong_count' => null, 'blank_count' => null, 'scholarship_percentage' => 0],
            $page->viewData('application')['result']);
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

    public function test_member_edit_keeps_its_full_session_and_saves_allowed_changes_without_replacing_identity(): void
    {
        $this->session->update(['capacity' => 1]);
        $application = $this->applications->create($this->student, $this->data());
        $edit = route('frontend.scholarship.applications.edit', $application->id);
        $update = route('frontend.scholarship.applications.update', $application->id);
        $show = route('frontend.scholarship.applications.show', $application->id);
        $this->school->update(['is_active' => false, 'name' => 'Renamed school']);
        $this->level->update(['is_active' => false]);
        $this->student->update(['name' => 'New profile name']);
        $page = $this->actingAs($this->student)->get($edit)->assertOk()
            ->assertSee($application->school_name_snapshot)->assertSee($application->student_name_snapshot);
        self::assertSame(0, $page->viewData('selectedSession')['remaining']);
        $dom = new DOMDocument;
        $dom->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);
        self::assertCount(1, $xpath->query('//select[@name="session_id"]/option[@selected and not(@disabled) and @value="'.$this->session->id.'"]'));
        self::assertCount(1, $xpath->query('//select[@name="school_id"]/option[@selected and @value="'.$this->school->id.'"]'));
        self::assertCount(1, $xpath->query('//form[@id="scholarship-application-form"]/input[@name="_method" and @value="PUT"]'));
        $this->put($update, $this->data())->assertRedirect($show)->assertSessionHasNoErrors();
        self::assertSame(1, $this->session->applications()->count());

        $school = $this->catalog->saveDefinition($this->admin, 'school', ['name' => 'New school']);
        $level = $this->catalog->saveDefinition($this->admin, 'student_level', ['code' => 'grade-12', 'name' => '12. Sınıf']);
        $this->put($update, [
            'session_id' => $this->alternative->id, 'school_id' => $school->id, 'student_level_id' => $level->id,
            'user_id' => $this->other->id, 'student_name' => 'Forged name', 'period_id' => 9999999,
            'status' => 'approved', 'result_published' => true, 'score' => 100, 'correct_count' => 100, 'asMember' => false,
        ])->assertRedirect($show)->assertSessionHasNoErrors();
        $fresh = $application->fresh();
        self::assertSame([$application->application_number, $this->student->id, $this->period->id, $application->student_name_snapshot, 'pending', null, null, false], [
            $fresh->application_number, $fresh->user_id, $fresh->period_id, $fresh->student_name_snapshot,
            $fresh->status, $fresh->score, $fresh->correct_count, $fresh->result_published,
        ]);
        self::assertSame([$this->alternative->id, $school->id, $level->id], [$fresh->session_id, $fresh->school_id, $fresh->student_level_id]);
        self::assertSame(0, $this->session->applications()->count());
        self::assertSame(1, $this->alternative->applications()->count());
        $this->get($show)->assertOk()->assertSee($school->name)->assertSee($level->name)->assertSee(__('scholarship.application_updated'));
    }

    public function test_member_edit_revalidates_target_and_keeps_the_original_seat_on_failure(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $edit = route('frontend.scholarship.applications.edit', $application->id);
        $update = route('frontend.scholarship.applications.update', $application->id);
        $this->actingAs($this->student)->get($edit)->assertOk();
        $this->alternative->update(['capacity' => 1]);
        $occupant = $this->applications->create($this->other, $this->data($this->alternative));
        $this->put($update, ['session_id' => $this->alternative->id])->assertRedirect($edit)->assertSessionHasErrors('session_id');
        $this->get($edit)->assertOk()->assertSee(__('scholarship.member_selection_unavailable'));
        $this->applications->delete($this->other, $occupant->id);
        $this->alternative->update(['is_active' => false]);
        $this->put($update, ['session_id' => $this->alternative->id])->assertSessionHasErrors('session_id');
        $this->alternative->update(['is_active' => true]);
        $period = $this->period->replicate();
        $period->save();
        $session = $this->alternative->replicate();
        $session->period_id = $period->id;
        $session->save();
        $this->put($update, ['session_id' => $session->id])->assertSessionHasErrors('session_id');
        $this->put($update, ['session_id' => ['invalid']])->assertSessionHasErrors('session_id');
        $this->get($edit)->assertOk();
        self::assertSame($this->session->id, $application->fresh()->session_id);
        self::assertSame(1, $this->session->applications()->count());
        self::assertSame(0, $this->alternative->applications()->count());
    }

    public function test_member_write_routes_enforce_ownership_and_all_locks_even_for_admin_accounts(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $edit = route('frontend.scholarship.applications.edit', $application->id);
        $update = route('frontend.scholarship.applications.update', $application->id);
        $delete = route('frontend.scholarship.applications.destroy', $application->id);
        $show = route('frontend.scholarship.applications.show', $application->id);
        $this->get($edit)->assertRedirect(route('login'));
        $this->put($update, ['session_id' => $this->alternative->id])->assertRedirect(route('login'));
        $this->delete($delete)->assertRedirect(route('login'));
        foreach ([$this->other, $this->admin] as $foreign) {
            $this->actingAs($foreign)->get($edit)->assertForbidden();
            $this->put($update, ['session_id' => $this->alternative->id])->assertForbidden();
            $this->delete($delete)->assertForbidden();
        }
        // The same account still uses member rules after becoming an admin.
        $this->student->forceFill(['type' => 'admin'])->save();
        $this->actingAs($this->student)->get($edit)->assertOk();
        foreach (['approval', 'closed', 'expired', 'suspended', 'archived'] as $lock) {
            $application->update(['status' => $lock === 'approval' ? 'approved' : 'pending']);
            $this->period->update(['applications_open' => $lock !== 'closed', 'applications_close_at' => $lock === 'expired' ? '2035-01-09 23:59:59' : '2035-01-20 23:59:59']);
            $this->session->update(['is_active' => $lock !== 'suspended', 'archived_at' => $lock === 'archived' ? now() : null]);
            $this->get($edit)->assertRedirect($show);
            $this->put($update, ['session_id' => $this->alternative->id, 'asMember' => false])->assertRedirect($edit)->assertSessionHasErrors('application');
            $this->delete($delete)->assertRedirect($show)->assertSessionHasErrors('application');
            $this->get($show)->assertOk()->assertDontSee('data-confirm-form="member-application-delete"', false)
                ->assertDontSee('href="'.$edit.'"', false)->assertDontSee(__('scholarship.delivery_accepted'));
            self::assertSame($this->session->id, $application->fresh()->session_id);
        }
        self::assertSame(1, $this->session->applications()->count());
        self::assertSame(0, $this->alternative->applications()->count());
    }

    public function test_member_permanent_delete_uses_confirmation_releases_seat_and_allows_reapplication(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $other = $this->applications->create($this->other, $this->data());
        $delete = route('frontend.scholarship.applications.destroy', $application->id);
        $show = route('frontend.scholarship.applications.show', $application->id);
        $page = $this->actingAs($this->student)->get($show)->assertOk()
            ->assertSee('data-confirm-form="member-application-delete"', false)
            ->assertSee('role="dialog"', false)->assertDontSee('wire:confirm')->assertDontSee('confirm(', false);
        $dom = new DOMDocument;
        $dom->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        self::assertCount(1, (new DOMXPath($dom))->query('//form[@id="member-application-delete"]//button[@type="button" and @data-action-confirmation]'));
        $this->delete($delete)->assertRedirect(route('frontend.scholarship.applications.index'))->assertSessionHasNoErrors();
        self::assertNull($application->fresh());
        self::assertNotNull($this->student->fresh());
        self::assertNotNull($other->fresh());
        self::assertSame(1, $this->session->applications()->count());
        $this->get(route('frontend.scholarship.applications.index'))->assertOk()->assertSee(__('scholarship.application_deleted'));
        $this->get($show)->assertNotFound();
        $this->post(route('frontend.scholarship.applications.store', $this->period->id), $this->data($this->alternative))
            ->assertSessionHasNoErrors();
        $replacement = ScholarshipApplication::query()->where('user_id', $this->student->id)->sole();
        self::assertNotSame($application->application_number, $replacement->application_number);
        self::assertSame($this->alternative->id, $replacement->session_id);
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    public function test_member_results_distinguish_missing_values_zero_and_absence_on_list_and_detail(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $list = route('frontend.scholarship.applications.index');
        $detail = route('frontend.scholarship.applications.show', $application->id);
        $this->actingAs($this->student);
        $publication = $this->applications->publish($this->admin, [$application->id], 'result');
        self::assertSame([], $publication['updated']);
        $page = $this->get($detail)->assertOk()->assertSee(__('scholarship.member_result_unpublished'));
        self::assertSame([], $this->displayedResult($page->getContent(), $application->id));
        self::assertSame(['published' => false], $page->viewData('application')['result']);

        $this->applications->updateResult($this->admin, $application->id, [
            'scholarship_percentage' => 0, 'correct_count' => 0, 'blank_count' => 3,
        ]);
        $this->applications->publish($this->admin, [$application->id], 'result');
        $expected = [
            'Burs Oranı' => '%0 / Burs Yok', 'Not' => 'Girilmedi', 'Katılım Durumu' => 'İşaretlenmedi',
            'Doğru Sayısı' => '0', 'Yanlış Sayısı' => 'Girilmedi', 'Boş Sayısı' => '3',
        ];
        foreach ([$list, $detail] as $url) {
            $page = $this->get($url)->assertOk()->assertSee(__('scholarship.member_result_published'))
                ->assertDontSee(__('scholarship.delivery_accepted'));
            self::assertSame($expected, $this->displayedResult($page->getContent(), $application->id));
        }

        $this->applications->updateResult($this->admin, $application->id, [
            'attendance_status' => 'attended', 'score' => 0, 'wrong_count' => 0, 'blank_count' => null,
        ]);
        $page = $this->get($detail)->assertOk();
        self::assertSame([...$expected, 'Not' => '0', 'Katılım Durumu' => 'Katıldı', 'Yanlış Sayısı' => '0', 'Boş Sayısı' => 'Girilmedi'],
            $this->displayedResult($page->getContent(), $application->id));

        $this->applications->updateResult($this->admin, $application->id, ['attendance_status' => 'absent']);
        $page = $this->get($detail)->assertOk();
        self::assertSame([
            'Burs Oranı' => '%0 / Burs Yok', 'Not' => '0', 'Katılım Durumu' => 'Katılmadı',
            'Doğru Sayısı' => 'Uygulanamaz', 'Yanlış Sayısı' => 'Uygulanamaz', 'Boş Sayısı' => 'Uygulanamaz',
        ], $this->displayedResult($page->getContent(), $application->id));
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    public function test_member_results_update_immediately_keep_history_and_disappear_when_unpublished(): void
    {
        $historical = $this->applications->create($this->student, $this->data());
        $this->applications->updateResult($this->admin, $historical->id, [
            'attendance_status' => 'attended', 'score' => 72, 'scholarship_percentage' => 40,
            'correct_count' => 60001, 'wrong_count' => 60002, 'blank_count' => 60003,
        ]);
        $this->applications->publish($this->admin, [$historical->id], 'result');
        $period = $this->period->replicate();
        $period->save();
        $session = $this->session->replicate();
        $session->period_id = $period->id;
        $session->save();
        $current = $this->applications->create($this->student, $this->data($session));
        $this->period->update([
            'is_active' => false, 'applications_open' => false,
            'applications_open_at' => '2034-01-01', 'applications_close_at' => '2034-01-20',
            'exam_starts_on' => '2034-01-25', 'exam_ends_on' => '2034-01-26',
        ]);
        $this->session->update(['exam_date' => '2034-01-25', 'archived_at' => now()]);
        $this->applications->updateResult($this->admin, $current->id, [
            'score' => 99, 'scholarship_percentage' => 100, 'correct_count' => 60004, 'wrong_count' => 60005, 'blank_count' => 60006,
        ]);
        $this->applications->publish($this->admin, [$current->id], 'result');
        $list = route('frontend.scholarship.applications.index');
        $detail = route('frontend.scholarship.applications.show', $current->id);
        $before = $this->actingAs($this->student)->get($list)->assertOk();
        $pastResult = $this->displayedResult($before->getContent(), $historical->id);
        self::assertSame('%40', $pastResult['Burs Oranı']);
        $this->applications->updateResult($this->admin, $current->id, [
            'score' => null, 'scholarship_percentage' => 70, 'correct_count' => null, 'wrong_count' => 12, 'blank_count' => 0,
        ]);
        $changed = $this->get($list)->assertOk();
        self::assertSame($pastResult, $this->displayedResult($changed->getContent(), $historical->id));
        $expected = [
            'Burs Oranı' => '%70', 'Not' => 'Girilmedi', 'Katılım Durumu' => 'İşaretlenmedi',
            'Doğru Sayısı' => 'Girilmedi', 'Yanlış Sayısı' => '12', 'Boş Sayısı' => '0',
        ];
        self::assertSame($expected, $this->displayedResult($changed->getContent(), $current->id));
        self::assertSame($expected, $this->displayedResult($this->get($detail)->assertOk()->getContent(), $current->id));

        $this->applications->publish($this->admin, [$historical->id, $current->id], 'result', false);
        foreach ([$list, $detail] as $url) {
            $hidden = $this->get($url)->assertOk()->assertSee(__('scholarship.member_result_unpublished'))
                ->assertDontSee('60001')->assertDontSee('60002')->assertDontSee('60003');
            self::assertSame([], $this->displayedResult($hidden->getContent(), $current->id));
            $data = $url === $list ? $hidden->viewData('applications')->first() : $hidden->viewData('application');
            self::assertSame(['published' => false], $data['result']);
        }
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    private function displayedResult(string $html, int $applicationId): array
    {
        $dom = new DOMDocument;
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $values = [];
        foreach ((new DOMXPath($dom))->query('//dl[@id="application-result-'.$applicationId.'"]/div') as $field) {
            $values[trim($field->getElementsByTagName('dt')->item(0)->textContent)] = trim($field->getElementsByTagName('dd')->item(0)->textContent);
        }

        return $values;
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
