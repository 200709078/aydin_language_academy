<?php

namespace Tests\Scholarship;

use App\Models\ScholarshipExamPeriod;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\ViewErrorBag;

class ScholarshipPeriodAdminTest extends ScholarshipTestCase
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

    public function test_period_routes_require_existing_admin_authentication(): void
    {
        $requests = [
            ['GET', $this->url('index')],
            ['GET', $this->url('create')],
            ['POST', $this->url('store')],
            ['GET', $this->url('edit', $this->period->id)],
            ['PUT', $this->url('update', $this->period->id)],
            ['PATCH', $this->url('applications.update', $this->period->id)],
            ['DELETE', $this->url('destroy', $this->period->id)],
        ];
        foreach ($requests as [$method, $url]) {
            $this->call($method, $url)->assertRedirect(route('login'));
        }
        $this->actingAs($this->student);
        foreach ($requests as [$method, $url]) {
            $this->call($method, $url)->assertForbidden();
        }
        self::assertSame(1, ScholarshipExamPeriod::query()->count());
        self::assertTrue($this->period->fresh()->applications_open);
    }

    public function test_period_pages_render_translations_safe_content_and_bound_forms(): void
    {
        $this->actingAs($this->admin);
        $title = '<script>alert("period")</script>';
        $this->catalog->savePeriod($this->admin, ['title' => $title], $this->period->id);
        foreach (['tr' => 'Sınav Dönemleri', 'en' => 'Exam Periods'] as $locale => $label) {
            $this->withSession(['locale' => $locale]);
            $this->get($this->url('index'))
                ->assertOk()
                ->assertSee($label)
                ->assertSee($title)
                ->assertDontSee($title, false)
                ->assertSee($this->url('create'), false)
                ->assertSee($this->url('edit', $this->period->id), false);
            $this->get($this->url('create'))
                ->assertOk()
                ->assertSee($this->url('store'), false)
                ->assertSee('name="_token"', false)
                ->assertSee('type="datetime-local"', false);
            $this->get($this->url('edit', $this->period->id))
                ->assertOk()
                ->assertSee($this->url('update', $this->period->id), false)
                ->assertSee('value="PUT"', false)
                ->assertSee('value="2035-01-20T23:59:59"', false)
                ->assertSee('value="2035-01-25"', false)
                ->assertSee($title)
                ->assertDontSee($title, false);
        }
    }

    public function test_admin_can_create_update_and_delete_an_unused_period(): void
    {
        $this->actingAs($this->admin);
        $data = $this->periodForm();
        $this->post($this->url('store'), $data)
            ->assertRedirect($this->url('index'))
            ->assertSessionHas('modalSuccessTitle')
            ->assertSessionHas('modalSuccessContent');
        $created = ScholarshipExamPeriod::query()->where('title', $data['title'])->sole();
        self::assertFalse($created->is_active);
        self::assertFalse($created->applications_open);
        self::assertSame('2035-02-01 09:10:11', $created->applications_open_at->format('Y-m-d H:i:s'));
        $this->put($this->url('update', $created->id), [...$data, 'title' => 'Updated period', 'is_active' => '1', 'applications_open' => '1'])
            ->assertRedirect($this->url('index'))
            ->assertSessionHas('modalSuccessContent');
        self::assertSame('Updated period', $created->fresh()->title);
        self::assertTrue($created->fresh()->is_active);
        self::assertTrue($created->fresh()->applications_open);
        $this->put($this->url('update', $created->id), [...$data, 'is_active' => '0', 'applications_open' => '0'])
            ->assertRedirect($this->url('index'));
        self::assertFalse($created->fresh()->is_active);
        self::assertFalse($created->fresh()->applications_open);
        $this->delete($this->url('destroy', $created->id))
            ->assertRedirect($this->url('index'))
            ->assertSessionHas('modalSuccessContent');
        self::assertNull($created->fresh());
        self::assertNotNull($this->period->fresh());
    }

    public function test_invalid_period_forms_preserve_input_and_existing_session_dates(): void
    {
        $this->actingAs($this->admin);
        $create = $this->url('create');
        $this->from($create)->post($this->url('store'), [...$this->periodForm(), 'applications_close_at' => '2035-01-01T00:00:00'])
            ->assertRedirect($create)
            ->assertSessionHasErrors(['applications_close_at'])
            ->assertSessionHasInput('title', 'New HTTP period');
        $this->get($create)->assertOk()->assertSee('value="New HTTP period"', false)->assertSee('is-invalid', false);
        self::assertSame(1, ScholarshipExamPeriod::query()->count());
        $this->from($create)->post($this->url('store'), [...$this->periodForm(), 'is_active' => 'invalid'])
            ->assertRedirect($create)->assertSessionHasErrors(['is_active']);
        $edit = $this->url('edit', $this->period->id);
        $this->from($edit)->put($this->url('update', $this->period->id), [
            'title' => 'Must not persist', 'description' => '',
            'applications_open_at' => '2035-01-01T00:00:00', 'applications_close_at' => '2035-01-20T23:59:59',
            'exam_starts_on' => '2035-01-26', 'exam_ends_on' => '2035-01-26',
            'is_active' => '1', 'applications_open' => '1',
        ])->assertRedirect($edit)->assertSessionHasErrors(['exam_starts_on']);
        self::assertSame('Test period', $this->period->fresh()->title);
        self::assertSame('2035-01-25', $this->period->fresh()->exam_starts_on->format('Y-m-d'));
    }

    public function test_manual_closure_uses_shared_member_rules_and_only_updates_the_switch(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->actingAs($this->admin);
        $url = $this->url('applications.update', $this->period->id);
        $this->patch($url, ['applications_open' => '0', 'title' => 'Forged title', 'is_active' => '0'])
            ->assertRedirect($this->url('index'))
            ->assertSessionHas('modalSuccessContent');
        self::assertFalse($this->period->fresh()->applications_open);
        self::assertTrue($this->period->fresh()->is_active);
        self::assertSame('Test period', $this->period->fresh()->title);
        $this->rejects(fn () => $this->applications->create($this->other, $this->data()));
        $this->rejects(fn () => $this->applications->update($this->student, $application->id, ['student_name' => 'Changed name']));
        $this->rejects(fn () => $this->applications->delete($this->student, $application->id));
        $visible = $this->applications->forMember($this->student, $application->id);
        self::assertSame($application->id, $visible['id']);
        self::assertFalse($visible['can_edit']);
        self::assertFalse($visible['can_delete']);
        $this->from($this->url('index'))->patch($url, ['applications_open' => 'invalid'])
            ->assertRedirect($this->url('index'))->assertSessionHasErrors(['applications_open']);
        self::assertFalse($this->period->fresh()->applications_open);
        $this->patch($url, ['applications_open' => '1'])->assertRedirect($this->url('index'));
        self::assertTrue($this->period->fresh()->acceptsApplications());
        self::assertTrue($this->applications->forMember($this->student, $application->id)['can_edit']);
    }

    public function test_period_dependencies_and_missing_records_are_reported_without_deletion(): void
    {
        $this->actingAs($this->admin);
        $this->from($this->url('index'))->delete($this->url('destroy', $this->period->id))
            ->assertRedirect($this->url('index'))->assertSessionHasErrors(['period_id']);
        self::assertNotNull($this->period->fresh());
        self::assertNotNull($this->session->fresh());
        foreach ([['GET', 'edit'], ['PUT', 'update'], ['PATCH', 'applications.update'], ['DELETE', 'destroy']] as [$method, $action]) {
            $this->call($method, $this->url($action, 99999999))->assertNotFound();
        }
    }

    public function test_list_distinguishes_manual_permission_from_current_availability_and_paginates(): void
    {
        $this->actingAs($this->admin);
        foreach (range(1, 18) as $number) {
            $this->catalog->savePeriod($this->admin, [...$this->periodForm(), 'title' => 'Period '.$number]);
        }
        $this->catalog->savePeriod($this->admin, [...$this->periodForm(), 'title' => 'Future period', 'is_active' => true, 'applications_open' => true]);
        $this->catalog->savePeriod($this->admin, [...$this->periodForm(), 'title' => 'Manually closed period', 'is_active' => true, 'applications_open' => false]);
        $this->get($this->url('index'))
            ->assertOk()
            ->assertSee(__('dictt.scholarship_applications_upcoming'))
            ->assertSee(__('dictt.scholarship_applications_closed_manually'))
            ->assertSee(__('dictt.scholarship_period_inactive'))
            ->assertSee('page=2', false)
            ->assertDontSee('Test period');
        $this->get($this->url('index').'?page=2')
            ->assertOk()
            ->assertSee('Test period')
            ->assertSee(__('dictt.scholarship_applications_accepting'))
            ->assertSee(__('dictt.scholarship_period_has_dependents'));
    }

    private function url(string $action, ?int $id = null): string
    {
        return route('admin.scholarship.periods.'.$action, $id === null ? [] : ['period' => $id]);
    }

    private function periodForm(): array
    {
        return [
            'title' => 'New HTTP period', 'description' => 'Period description',
            'applications_open_at' => '2035-02-01T09:10:11', 'applications_close_at' => '2035-02-20T23:59:59',
            'exam_starts_on' => '2035-02-25', 'exam_ends_on' => '2035-02-26',
        ];
    }
}
