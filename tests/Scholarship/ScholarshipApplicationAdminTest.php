<?php

namespace Tests\Scholarship;

use App\Models\ScholarshipApplication;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ViewErrorBag;

class ScholarshipApplicationAdminTest extends ScholarshipTestCase
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

    public function test_all_application_routes_require_existing_admin_authentication(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $requests = [['GET', 'index', null], ['GET', 'show', $application->id], ['GET', 'edit', $application->id],
            ['PUT', 'update', $application->id], ['PATCH', 'approve', $application->id], ['DELETE', 'destroy', $application->id],
            ['POST', 'approval.preview', null], ['POST', 'approve.bulk', null]];
        foreach ($requests as [$method, $route, $id]) {
            $this->call($method, $this->url($route, $id))->assertRedirect(route('login'));
        }
        $this->actingAs($this->student);
        foreach ($requests as [$method, $route, $id]) {
            $this->call($method, $this->url($route, $id))->assertForbidden();
        }
        self::assertSame('pending', $application->fresh()->status);
        self::assertSame(1, ScholarshipApplication::query()->count());
    }

    public function test_pages_escape_student_data_translate_labels_and_handle_deleted_accounts(): void
    {
        $name = '<script>alert("student")</script>';
        $application = $this->applications->create($this->student, [...$this->data(), 'student_name' => $name]);
        $this->student->update(['name' => 'Current account name', 'email' => 'current@example.invalid', 'phone' => '5551234567']);
        $this->actingAs($this->admin);
        foreach (['tr', 'en'] as $locale) {
            $this->withSession(['locale' => $locale]);
            $this->app->setLocale($locale);
            $this->get($this->url('index'))->assertOk()->assertSee(__('scholarship.applications'))->assertSee($application->application_number)
                ->assertSee($name)->assertDontSee($name, false)->assertSee('current@example.invalid')->assertSee('5551234567')
                ->assertSee('data-action-confirmation', false)->assertSee($this->url('approval.preview'), false);
            $this->get($this->url('show', $application->id))->assertOk()->assertSee($name)->assertDontSee($name, false)
                ->assertSee('Current school')->assertSee('1. Sınıf')->assertSee('Lise')->assertSee('Current account name');
            $this->get($this->url('edit', $application->id))->assertOk()->assertSee($name)->assertDontSee($name, false)
                ->assertSee($this->url('update', $application->id), false)->assertSee('name="_token"', false)->assertSee('value="PUT"', false);
        }
        $this->student->delete();
        $this->get($this->url('show', $application->id))->assertOk()->assertSee(__('scholarship.account_deleted'))->assertSee($application->application_number);
        $this->get($this->url('index'))->assertOk()->assertSee($application->application_number);
    }

    public function test_single_approval_is_idempotent_and_does_not_publish_or_send_messages(): void
    {
        Mail::fake();
        Notification::fake();
        $application = $this->applications->create($this->student, $this->data());
        $this->actingAs($this->admin)->patch($this->url('approve', $application->id), ['application_published' => true])
            ->assertRedirect($this->url('show', $application->id))->assertSessionHas('modalSuccessContent');
        self::assertSame('approved', $application->fresh()->status);
        self::assertFalse($application->fresh()->application_published);
        self::assertFalse($this->applications->forMember($this->student, $application->id)['can_delete']);
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->patch($this->url('approve', $application->id))->assertRedirect();
        self::assertSame('reached', $application->fresh()->application_contact_status);
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
        Notification::assertNothingSent();
    }

    public function test_admin_transfer_enforces_capacity_and_preserves_approved_published_state(): void
    {
        Mail::fake();
        Notification::fake();
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->create($this->other, $this->data($this->alternative));
        $this->applications->create($this->third, $this->data($this->alternative));
        $this->applications->approve($this->admin, $application->id);
        $this->applications->publish($this->admin, [$application->id], 'application');
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $this->actingAs($this->admin);
        $edit = $this->url('edit', $application->id);
        $this->from($edit)->put($this->url('update', $application->id), [...$this->form($application), 'session_id' => $this->alternative->id])
            ->assertRedirect($edit)->assertSessionHasErrors('session_id')->assertSessionHasInput('student_name', $application->student_name_snapshot);
        self::assertSame($this->session->id, $application->fresh()->session_id);
        self::assertSame('reached', $application->fresh()->application_contact_status);
        $this->catalog->saveSession($this->admin, ['capacity' => 3], $this->alternative->id);
        $this->catalog->archiveSession($this->admin, $this->session->id);
        $this->catalog->savePeriod($this->admin, ['applications_open' => false], $this->period->id);
        $level = $this->catalog->saveDefinition($this->admin, 'student_level', ['code' => 'yks', 'name' => 'YKS']);
        $this->put($this->url('update', $application->id), [...$this->form($application), 'student_name' => 'Updated student',
            'session_id' => $this->alternative->id, 'student_level_id' => $level->id,
            'user_id' => $this->other->id, 'period_id' => 99999, 'status' => 'pending', 'score' => 100, 'application_published' => false])
            ->assertRedirect($this->url('show', $application->id));
        $fresh = $application->fresh();
        self::assertSame($this->alternative->id, $fresh->session_id);
        self::assertSame($this->student->id, $fresh->user_id);
        self::assertSame($this->period->id, $fresh->period_id);
        self::assertSame($application->application_number, $fresh->application_number);
        self::assertSame('approved', $fresh->status);
        self::assertTrue($fresh->application_published);
        self::assertNull($fresh->score);
        self::assertSame('unreached', $fresh->application_contact_status);
        self::assertSame('reached', $fresh->result_contact_status);
        self::assertSame('YKS', $fresh->student_level_name_snapshot);
        self::assertSame('Updated student', $this->applications->forMember($this->student, $fresh->id)['student']['name']);
        self::assertSame(0, $this->session->applications()->count());
        self::assertSame(3, $this->alternative->applications()->count());
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_missing_fields_and_cross_period_transfers_are_rejected_without_losing_the_application(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->actingAs($this->admin);
        $edit = $this->url('edit', $application->id);
        $this->from($edit)->put($this->url('update', $application->id), ['student_name' => 'Name only'])
            ->assertRedirect($edit)->assertSessionHasErrors(['school_id', 'student_level_id', 'session_id']);
        $period = $this->catalog->savePeriod($this->admin, ['title' => 'Other period', 'applications_open_at' => '2035-01-01 00:00:00',
            'applications_close_at' => '2035-01-20 23:59:59', 'exam_starts_on' => '2035-01-25', 'exam_ends_on' => '2035-01-26']);
        $session = $this->catalog->saveSession($this->admin, ['period_id' => $period->id, 'branch_id' => $this->branch->id,
            'exam_group_id' => $this->group->id, 'exam_title' => 'Other exam', 'exam_date' => '2035-01-25', 'starts_at' => '08:00', 'ends_at' => '10:00', 'capacity' => 10]);
        $this->from($edit)->put($this->url('update', $application->id), [...$this->form($application), 'session_id' => $session->id])
            ->assertRedirect($edit)->assertSessionHasErrors('session_id');
        self::assertSame($this->session->id, $application->fresh()->session_id);
        foreach (['show' => 'GET', 'edit' => 'GET', 'update' => 'PUT', 'approve' => 'PATCH', 'destroy' => 'DELETE'] as $route => $method) {
            $this->call($method, $this->url($route, 99999999))->assertNotFound();
        }
    }

    public function test_filters_search_and_zero_values_distinguish_records_without_crossing_relations(): void
    {
        $first = $this->applications->create($this->student, [...$this->data(), 'student_name' => 'Snapshot one']);
        $second = $this->applications->create($this->other, [...$this->data($this->alternative), 'student_name' => 'Snapshot two']);
        $this->applications->approve($this->admin, $first->id);
        $this->applications->updateResult($this->admin, $first->id, ['attendance_status' => 'absent']);
        $this->applications->markContact($this->admin, $first->id, 'application', true);
        $this->applications->markContact($this->admin, $second->id, 'result', true);
        $this->catalog->saveSession($this->admin, ['is_active' => false], $this->alternative->id);
        $this->student->update(['name' => 'Current distinct name', 'email' => 'unique@example.invalid', 'phone' => '1234567890']);
        $this->actingAs($this->admin);
        $combined = ['period_id' => $this->period->id, 'branch_id' => $this->branch->id, 'exam_group_id' => $this->group->id,
            'session_id' => $this->session->id, 'school_id' => $this->school->id, 'student_level_id' => $this->level->id,
            'exam_date' => '2035-01-25', 'starts_at' => '08:00', 'ends_at' => '10:00:00', 'status' => 'approved',
            'attendance_status' => 'absent', 'application_contact_status' => 'reached', 'result_contact_status' => 'unreached',
            'application_published' => '0', 'result_published' => '0', 'scholarship_percentage' => '0', 'session_state' => 'active'];
        $this->get($this->url('index').'?'.http_build_query($combined))->assertOk()
            ->assertViewHas('applications', fn ($items) => $items->total() === 1 && $items->first()->id === $first->id);
        $this->get($this->url('index').'?scholarship_percentage=unset&session_state=suspended&result_contact_status=reached')->assertOk()
            ->assertViewHas('applications', fn ($items) => $items->total() === 1 && $items->first()->id === $second->id);
        foreach ([$first->application_number, 'Snapshot one', 'Current distinct name', 'unique@example.invalid', '1234567890'] as $search) {
            $this->get($this->url('index').'?q='.urlencode($search))->assertOk()
                ->assertViewHas('applications', fn ($items) => $items->total() === 1 && $items->first()->id === $first->id);
        }
        $this->get($this->url('index').'?q=unique&branch_id='.$this->otherBranch->id)->assertOk()
            ->assertViewHas('applications', fn ($items) => $items->total() === 0);
        $this->from($this->url('index'))->get($this->url('index').'?status=rejected&starts_at=invalid&branch_id=99999999')
            ->assertRedirect($this->url('index'))->assertSessionHasErrors(['status', 'starts_at', 'branch_id']);
    }

    public function test_selected_bulk_approval_requires_a_valid_actor_bound_preview_and_ignores_posted_ids(): void
    {
        $first = $this->applications->create($this->student, $this->data());
        $second = $this->applications->create($this->other, $this->data($this->alternative));
        $this->actingAs($this->admin);
        $this->from($this->url('index'))->post($this->url('approval.preview'), ['scope' => 'selected', 'ids' => [$second->id], 'filters' => ['branch_id' => $this->branch->id]])
            ->assertRedirect($this->url('index'))->assertSessionHasErrors('ids');
        $this->post($this->url('approval.preview'), ['scope' => 'selected', 'ids' => [$first->id]])->assertOk()
            ->assertViewHas('count', 1)->assertSee('data-action-confirmation', false);
        self::assertSame('pending', $first->fresh()->status);
        $token = session('scholarship_bulk_approval.token');
        $this->from($this->url('index'))->post($this->url('approve.bulk'), ['token' => str_repeat('x', 40)])
            ->assertRedirect($this->url('index'))->assertSessionHasErrors('token');
        $otherAdmin = $this->other->forceFill(['type' => 'admin']);
        $otherAdmin->save();
        $this->actingAs($otherAdmin)->from($this->url('index'))->post($this->url('approve.bulk'), ['token' => $token])
            ->assertRedirect($this->url('index'))->assertSessionHasErrors('token');
        $this->actingAs($this->admin)->post($this->url('approve.bulk'), ['token' => $token, 'ids' => [$second->id], 'scope' => 'filtered'])
            ->assertRedirect($this->url('index'))->assertSessionHas('modalSuccessContent');
        self::assertSame('approved', $first->fresh()->status);
        self::assertSame('pending', $second->fresh()->status);
        self::assertFalse($first->fresh()->application_published);
        $this->from($this->url('index'))->post($this->url('approve.bulk'), ['token' => $token])->assertSessionHasErrors('token');
    }

    public function test_filtered_bulk_approval_spans_pages_freezes_selection_and_skips_deleted_rows(): void
    {
        $this->catalog->saveSession($this->admin, ['capacity' => 30], $this->session->id);
        $ids = [];
        foreach (range(1, 23) as $n) {
            $user = User::query()->create(['name' => 'Paged student '.$n, 'email' => 'paged'.$n.'@example.invalid', 'password' => 'unused-synthetic-password']);
            $ids[] = $this->applications->create($user, $this->data())->id;
        }
        $outside = $this->applications->create($this->other, $this->data($this->alternative));
        $this->actingAs($this->admin);
        $this->get($this->url('index').'?branch_id='.$this->branch->id.'&page=2')->assertOk()
            ->assertViewHas('applications', fn ($items) => $items->total() === 23 && $items->count() === 3)->assertViewHas('pendingCount', 23);
        $this->post($this->url('approval.preview'), ['scope' => 'filtered', 'filters' => ['branch_id' => $this->branch->id]])
            ->assertOk()->assertViewHas('count', 23)->assertViewHas('applications', fn ($items) => $items->count() === 20);
        $token = session('scholarship_bulk_approval.token');
        $late = $this->applications->create($this->student, $this->data());
        $this->applications->delete($this->admin, $ids[0]);
        $this->post($this->url('approve.bulk'), ['token' => $token])->assertRedirect($this->url('index'))
            ->assertSessionHas('modalSuccessContent', __('scholarship.bulk_approval_completed', ['approved' => 22, 'skipped' => 1]));
        self::assertSame(22, ScholarshipApplication::query()->where('status', 'approved')->count());
        self::assertSame('pending', $late->fresh()->status);
        self::assertSame('pending', $outside->fresh()->status);
    }

    public function test_empty_and_expired_previews_do_not_approve_and_deletion_releases_the_period_slot(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->actingAs($this->admin);
        $this->from($this->url('index'))->post($this->url('approval.preview'), ['scope' => 'selected'])
            ->assertRedirect($this->url('index'))->assertSessionHasErrors('ids');
        $this->post($this->url('approval.preview'), ['scope' => 'filtered'])->assertOk();
        $preview = session('scholarship_bulk_approval');
        $preview['expires_at'] = now()->subMinute()->timestamp;
        $this->withSession(['scholarship_bulk_approval' => $preview])->from($this->url('index'))
            ->post($this->url('approve.bulk'), ['token' => $preview['token']])->assertSessionHasErrors('token');
        self::assertSame('pending', $application->fresh()->status);
        $this->applications->approve($this->admin, $application->id);
        $this->delete($this->url('destroy', $application->id))->assertRedirect($this->url('index'));
        self::assertNull($application->fresh());
        self::assertNotNull($this->student->fresh());
        self::assertNotNull($this->period->fresh());
        self::assertSame(0, $this->session->applications()->count());
        $again = $this->applications->create($this->student, $this->data($this->alternative));
        self::assertNotSame($application->application_number, $again->application_number);
        self::assertSame($this->period->id, $again->period_id);
    }

    private function url(string $action, ?int $id = null): string
    {
        return route('admin.scholarship.applications.'.$action, $id === null ? [] : ['application' => $id]);
    }

    private function form(ScholarshipApplication $application): array
    {
        return ['student_name' => $application->student_name_snapshot, 'school_id' => $application->school_id,
            'student_level_id' => $application->student_level_id, 'session_id' => $application->session_id];
    }
}
