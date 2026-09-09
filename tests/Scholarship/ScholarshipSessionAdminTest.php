<?php

namespace Tests\Scholarship;

use App\Models\ScholarshipExamSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\ViewErrorBag;

class ScholarshipSessionAdminTest extends ScholarshipTestCase
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

    public function test_session_routes_require_existing_admin_authentication(): void
    {
        $requests = [
            ['GET', 'index', null], ['GET', 'create', null], ['POST', 'store', null],
            ['GET', 'edit', $this->session->id], ['PUT', 'update', $this->session->id],
            ['PATCH', 'status.update', $this->session->id], ['PATCH', 'archive.update', $this->session->id],
            ['DELETE', 'destroy', $this->session->id],
        ];
        foreach ($requests as [$method, $action, $id]) {
            $this->call($method, $this->url($action, $id))->assertRedirect(route('login'));
        }
        $this->actingAs($this->student);
        foreach ($requests as [$method, $action, $id]) {
            $this->call($method, $this->url($action, $id))->assertForbidden();
        }
        self::assertSame(2, ScholarshipExamSession::query()->count());
        self::assertTrue($this->session->fresh()->is_active);
        self::assertNull($this->session->fresh()->archived_at);
    }

    public function test_session_pages_render_safe_translated_content_choices_and_bound_forms(): void
    {
        $this->actingAs($this->admin);
        $title = '<script>alert("session")</script>';
        $this->catalog->saveSession($this->admin, ['exam_title' => $title], $this->session->id);
        $this->catalog->saveDefinition($this->admin, 'branch', ['is_active' => false], $this->otherBranch->id);
        foreach (['tr', 'en'] as $locale) {
            $this->withSession(['locale' => $locale]);
            $this->app->setLocale($locale);
            $this->get($this->url('index'))
                ->assertOk()->assertSee(__('scholarship.sessions'))
                ->assertSee($title)->assertDontSee($title, false)
                ->assertSee($this->url('edit', $this->session->id), false)
                ->assertSee('data-action-confirmation', false);
            $this->get($this->url('create').'?period_id='.$this->period->id)
                ->assertOk()->assertViewHas('selectedPeriodId', (string) $this->period->id)
                ->assertSee($this->url('store'), false)->assertSee('name="_token"', false)
                ->assertSee('min="2035-01-25"', false)->assertSee('max="2035-01-26"', false)
                ->assertSee('type="time" step="1"', false)
                ->assertSee('Branch B ('.__('dictt.passive').')');
            $this->get($this->url('edit', $this->session->id))
                ->assertOk()->assertSee($this->url('update', $this->session->id), false)
                ->assertSee('value="PUT"', false)->assertSee('name="period_id" value="'.$this->period->id.'"', false)
                ->assertSee('value="08:00:00"', false)->assertSee(__('scholarship.session_period_fixed'))
                ->assertSee($title)->assertDontSee($title, false);
        }
    }

    public function test_admin_can_create_update_and_delete_an_unused_session(): void
    {
        $this->actingAs($this->admin);
        $data = $this->sessionForm();
        $this->post($this->url('store'), [...$data, 'archived_at' => '2035-01-10 12:00:00'])
            ->assertRedirect($this->url('index'))->assertSessionHas('modalSuccessContent');
        $created = ScholarshipExamSession::query()->where('exam_title', $data['exam_title'])->sole();
        self::assertTrue($created->is_active);
        self::assertNull($created->archived_at);
        self::assertSame('10:15:00', $created->starts_at);
        self::assertSame(15, $created->capacity);
        $this->put($this->url('update', $created->id), [...$data, 'exam_title' => 'Updated HTTP exam', 'capacity' => '25', 'is_active' => '0'])
            ->assertRedirect($this->url('index'))->assertSessionHas('modalSuccessContent');
        self::assertSame('Updated HTTP exam', $created->fresh()->exam_title);
        self::assertSame(25, $created->fresh()->capacity);
        self::assertFalse($created->fresh()->is_active);
        $this->delete($this->url('destroy', $created->id))
            ->assertRedirect($this->url('index'))->assertSessionHas('modalSuccessContent');
        self::assertNull($created->fresh());
        self::assertNotNull($this->session->fresh());
    }

    public function test_invalid_session_forms_preserve_input_and_domain_boundaries(): void
    {
        $this->actingAs($this->admin);
        $create = $this->url('create');
        foreach ([
            'exam_date' => '2035-01-27', 'ends_at' => '09:00', 'capacity' => '1.5', 'is_active' => 'invalid',
        ] as $field => $invalid) {
            $this->from($create)->post($this->url('store'), [...$this->sessionForm(), $field => $invalid])
                ->assertRedirect($create)->assertSessionHasErrors([$field])
                ->assertSessionHasInput('exam_title', 'New HTTP exam');
        }
        $this->get($create)->assertOk()->assertSee('value="New HTTP exam"', false)->assertSee('is-invalid', false);
        $this->from($create)->post($this->url('store'), $this->sessionForm($this->session))
            ->assertRedirect($create)->assertSessionHasErrors(['starts_at']);
        $edit = $this->url('edit', $this->session->id);
        $this->from($edit)->put($this->url('update', $this->session->id), ['exam_title' => 'Missing required values'])
            ->assertRedirect($edit)->assertSessionHasErrors(['period_id', 'branch_id', 'exam_group_id', 'exam_date', 'starts_at', 'ends_at', 'capacity']);
        $anotherPeriod = $this->catalog->savePeriod($this->admin, [
            'title' => 'Other period', 'applications_open_at' => '2035-01-01 00:00:00', 'applications_close_at' => '2035-01-20 23:59:59',
            'exam_starts_on' => '2035-01-25', 'exam_ends_on' => '2035-01-26',
        ]);
        $this->from($edit)->put($this->url('update', $this->session->id), [...$this->sessionForm($this->session), 'period_id' => $anotherPeriod->id])
            ->assertRedirect($edit)->assertSessionHasErrors(['period_id']);
        self::assertSame($this->period->id, $this->session->fresh()->period_id);
        self::assertSame('Test exam', $this->session->fresh()->exam_title);
        self::assertSame(2, ScholarshipExamSession::query()->count());
    }

    public function test_occupied_capacity_is_enforced_and_admin_changes_reset_only_application_contact(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->create($this->other, $this->data());
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $this->actingAs($this->admin);
        $edit = $this->url('edit', $this->session->id);
        $this->get($edit)->assertOk()->assertSee('min="2"', false)
            ->assertViewHas('session', fn ($session) => $session->applications_count === 2);
        $this->from($edit)->put($this->url('update', $this->session->id), [...$this->sessionForm($this->session), 'capacity' => '1'])
            ->assertRedirect($edit)->assertSessionHasErrors(['capacity']);
        self::assertSame(2, $this->session->fresh()->capacity);
        $this->put($this->url('update', $this->session->id), [...$this->sessionForm($this->session), 'exam_title' => 'Rescheduled exam', 'capacity' => '3'])
            ->assertRedirect($this->url('index'));
        self::assertSame(3, $this->session->fresh()->capacity);
        self::assertSame('unreached', $application->fresh()->application_contact_status);
        self::assertSame('reached', $application->fresh()->result_contact_status);
        self::assertSame(0, $application->notifications()->count());
    }

    public function test_pause_archive_and_restore_preserve_applications_and_restrict_only_the_selected_session(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->updateResult($this->admin, $application->id, ['score' => 70, 'scholarship_percentage' => 20]);
        $this->actingAs($this->admin);
        $status = $this->url('status.update', $this->session->id);
        $archive = $this->url('archive.update', $this->session->id);
        $this->patch($status, ['is_active' => '0', 'capacity' => 99, 'exam_title' => 'Forged'])
            ->assertRedirect($this->url('index'));
        self::assertFalse($this->session->fresh()->is_active);
        self::assertSame(2, $this->session->fresh()->capacity);
        self::assertSame('Test exam', $this->session->fresh()->exam_title);
        self::assertTrue($this->alternative->fresh()->is_active);
        self::assertTrue($this->period->fresh()->applications_open);
        self::assertSame('suspended', $this->applications->forMember($this->student, $application->id)['restriction']);
        $this->rejects(fn () => $this->applications->update($this->student, $application->id, ['session_id' => $this->alternative->id]));
        $this->rejects(fn () => $this->applications->delete($this->student, $application->id));
        $this->patch($archive, ['archived' => '1', 'is_active' => '1'])->assertRedirect($this->url('index'));
        self::assertNotNull($this->session->fresh()->archived_at);
        self::assertFalse($this->session->fresh()->is_active);
        self::assertSame('archived', $this->applications->forMember($this->student, $application->id)['restriction']);
        $this->put($this->url('update', $this->session->id), [...$this->sessionForm($this->session), 'capacity' => '3', 'is_active' => '0'])
            ->assertRedirect($this->url('index'));
        self::assertSame(3, $this->session->fresh()->capacity);
        $this->from($this->url('index'))->delete($this->url('destroy', $this->session->id))
            ->assertRedirect($this->url('index'))->assertSessionHasErrors(['session_id']);
        self::assertSame(70, $application->fresh()->score);
        self::assertSame(20, $application->fresh()->scholarship_percentage);
        self::assertSame($this->session->id, $application->fresh()->session_id);
        $this->patch($archive, ['archived' => '0'])->assertRedirect($this->url('index'));
        self::assertNull($this->session->fresh()->archived_at);
        self::assertFalse($this->session->fresh()->is_active);
        $this->patch($status, ['is_active' => '1'])->assertRedirect($this->url('index'));
        self::assertTrue($this->applications->forMember($this->student, $application->id)['can_edit']);
        foreach ([[$status, 'is_active'], [$archive, 'archived']] as [$url, $field]) {
            $this->from($this->url('index'))->patch($url, [$field => 'invalid'])
                ->assertRedirect($this->url('index'))->assertSessionHasErrors([$field]);
            $this->from($this->url('index'))->patch($url, [])
                ->assertRedirect($this->url('index'))->assertSessionHasErrors([$field]);
        }
        foreach ([['GET', 'edit'], ['PUT', 'update'], ['PATCH', 'status.update'], ['PATCH', 'archive.update'], ['DELETE', 'destroy']] as [$method, $action]) {
            $this->call($method, $this->url($action, 99999999))->assertNotFound();
        }
    }

    public function test_session_filters_and_totals_cover_all_filtered_records_including_full_sessions(): void
    {
        $this->actingAs($this->admin);
        $this->applications->create($this->student, $this->data());
        $this->applications->create($this->other, $this->data());
        foreach (range(1, 21) as $number) {
            $this->catalog->saveSession($this->admin, [...$this->sessionForm(), 'exam_title' => 'Filtered exam '.$number, 'capacity' => 10]);
        }
        $this->catalog->saveSession($this->admin, ['is_active' => false], $this->alternative->id);
        $this->get($this->url('index').'?state=active&branch_id='.$this->branch->id)
            ->assertOk()->assertViewHas('totals', ['sessions' => 22, 'capacity' => 212, 'applications' => 2])
            ->assertViewHas('sessions', fn ($sessions) => $sessions->count() === 20 && $sessions->total() === 22)
            ->assertSee(__('scholarship.session_full'))->assertSee('2 / 2')
            ->assertSee('state=active', false)->assertSee('page=2', false);
        $this->get($this->url('index').'?state=active&branch_id='.$this->branch->id.'&page=2')
            ->assertOk()->assertViewHas('totals', ['sessions' => 22, 'capacity' => 212, 'applications' => 2])
            ->assertViewHas('sessions', fn ($sessions) => $sessions->count() === 2);
        $this->get($this->url('index').'?period_id='.$this->period->id.'&exam_group_id='.$this->group->id.'&q=Filtered')
            ->assertOk()->assertViewHas('totals', ['sessions' => 21, 'capacity' => 210, 'applications' => 0]);
        $this->get($this->url('index').'?state=suspended')
            ->assertOk()->assertViewHas('totals', ['sessions' => 1, 'capacity' => 2, 'applications' => 0]);
        $this->catalog->archiveSession($this->admin, $this->alternative->id);
        $this->get($this->url('index').'?state=archived')
            ->assertOk()->assertViewHas('totals', ['sessions' => 1, 'capacity' => 2, 'applications' => 0]);
        $this->get($this->url('index').'?state=suspended')
            ->assertOk()->assertSee(__('scholarship.sessions_empty'))
            ->assertViewHas('totals', ['sessions' => 0, 'capacity' => 0, 'applications' => 0]);
        $this->from($this->url('index'))->get($this->url('index').'?state=unknown&branch_id=99999999')
            ->assertRedirect($this->url('index'))->assertSessionHasErrors(['state', 'branch_id']);
    }

    private function url(string $action, ?int $id = null): string
    {
        return route('admin.scholarship.sessions.'.$action, $id === null ? [] : ['session' => $id]);
    }

    private function sessionForm(?ScholarshipExamSession $session = null): array
    {
        if ($session !== null) {
            return [
                'period_id' => $session->period_id, 'branch_id' => $session->branch_id, 'exam_group_id' => $session->exam_group_id,
                'exam_title' => $session->exam_title, 'exam_date' => $session->exam_date->format('Y-m-d'),
                'starts_at' => $session->starts_at, 'ends_at' => $session->ends_at, 'capacity' => $session->capacity, 'is_active' => $session->is_active ? '1' : '0',
            ];
        }

        return [
            'period_id' => $this->period->id, 'branch_id' => $this->branch->id, 'exam_group_id' => $this->group->id,
            'exam_title' => 'New HTTP exam', 'exam_date' => '2035-01-25', 'starts_at' => '10:15', 'ends_at' => '12:30',
            'capacity' => '15', 'is_active' => '1',
        ];
    }
}
