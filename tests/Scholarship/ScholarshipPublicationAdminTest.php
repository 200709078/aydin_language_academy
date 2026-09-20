<?php

namespace Tests\Scholarship;

use DOMDocument;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ViewErrorBag;

class ScholarshipPublicationAdminTest extends ScholarshipTestCase
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

    public function test_publication_routes_require_admin_and_valid_phase(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $routes = [['PATCH', $this->url('publication.update', ['application' => $application->id, 'phase' => 'result'])],
            ['POST', $this->url('publication.preview')], ['POST', $this->url('publication.bulk')]];
        foreach ($routes as [$method, $url]) {
            $this->call($method, $url)->assertRedirect(route('login'));
        }
        $this->actingAs($this->student);
        foreach ($routes as [$method, $url]) {
            $this->call($method, $url)->assertForbidden();
        }
        $this->actingAs($this->admin)->patch($this->url('publication.update', [
            'application' => $application->id, 'phase' => 'status',
        ]), ['published' => 1])->assertNotFound();
        self::assertFalse($application->fresh()->result_published);
    }

    public function test_independent_switches_preserve_approval_contact_and_zero_results_without_messages(): void
    {
        Mail::fake();
        Notification::fake();
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->approve($this->admin, $application->id);
        self::assertSame('under_review', $this->applications->forMember($this->student, $application->id)['application']['state']);
        $show = $this->url('show', ['application' => $application->id]);
        $applicationSwitch = $this->url('publication.update', ['application' => $application->id, 'phase' => 'application']);
        $resultSwitch = $this->url('publication.update', ['application' => $application->id, 'phase' => 'result']);
        $page = $this->actingAs($this->admin)->get($show)->assertOk()->assertSee($applicationSwitch)->assertSee($resultSwitch);
        $dom = new DOMDocument;
        $dom->loadHTML($page->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        self::assertTrue($dom->getElementById('publication-'.$application->id.'-result')->hasAttribute('disabled'));
        $this->from($show)->patch($applicationSwitch, ['published' => 1, 'status' => 'pending', 'score' => 99])
            ->assertRedirect($show)->assertSessionHasNoErrors();
        self::assertSame('approved', $this->applications->forMember($this->student, $application->id)['application']['state']);
        self::assertSame('2035-01-25 07:30:00', $this->applications->forMember($this->student, $application->id)['application']['arrival_at']);
        self::assertNull($application->fresh()->score);
        $this->patch($resultSwitch, ['published' => 1])->assertSessionHasErrors('result_published');
        $this->applications->updateResult($this->admin, $application->id, ['score' => 0]);
        $preview = $this->post($this->url('publication.preview'), [
            'scope' => 'selected', 'ids' => [$application->id], 'phase' => 'result', 'published' => 1,
        ])->assertOk()->assertViewHas('eligibleCount', 0);
        $this->post($this->url('publication.bulk'), ['token' => $preview->viewData('token')])->assertRedirect();
        self::assertFalse($application->fresh()->result_published);
        self::assertSame($application->application_number, session('publicationSkipped.0.number'));

        $this->applications->updateResult($this->admin, $application->id, ['score' => null, 'scholarship_percentage' => 0]);
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->applications->markContact($this->admin, $application->id, 'result', true);
        $this->flushSession();
        $this->patch($resultSwitch, ['published' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $result = $this->applications->forMember($this->student, $application->id)['result'];
        self::assertSame([true, null, 0], [$result['published'], $result['score'], $result['scholarship_percentage']]);
        $this->patch($applicationSwitch, ['published' => 0])->assertRedirect()->assertSessionHasNoErrors();
        self::assertFalse($application->fresh()->application_published);
        self::assertTrue($application->fresh()->result_published);
        $this->patch($resultSwitch, ['published' => 0])->assertRedirect()->assertSessionHasNoErrors();
        self::assertSame(['published' => false], $this->applications->forMember($this->student, $application->id)['result']);
        $fresh = $application->fresh();
        self::assertSame(['approved', 'reached', 'reached'], [$fresh->status, $fresh->application_contact_status, $fresh->result_contact_status]);
        Mail::assertNothingSent();
        Mail::assertNothingQueued();
        Notification::assertNothingSent();
    }

    public function test_selected_publication_rechecks_results_and_ignores_forged_confirmation_scope(): void
    {
        $first = $this->applications->create($this->student, $this->data());
        $second = $this->applications->create($this->other, $this->data());
        $outside = $this->applications->create($this->third, $this->data($this->alternative));
        foreach ([$first, $second] as $application) {
            $this->applications->updateResult($this->admin, $application->id, ['scholarship_percentage' => 0]);
        }
        $this->actingAs($this->admin);
        $preview = $this->post($this->url('publication.preview'), ['scope' => 'selected',
            'ids' => [$first->id, $second->id], 'phase' => 'result', 'published' => 1,
        ])->assertOk()->assertViewHas('count', 2)->assertViewHas('eligibleCount', 2)->assertSee('data-action-confirmation', false);
        self::assertFalse($first->fresh()->result_published);
        $this->applications->updateResult($this->admin, $second->id, ['scholarship_percentage' => null]);
        $token = $preview->viewData('token');
        $this->post($this->url('publication.bulk'), ['token' => $token,
            'phase' => 'application', 'published' => 0, 'ids' => [$outside->id], 'scope' => 'filtered',
        ])->assertRedirect($this->url('index'))->assertSessionHas('modalSuccessContent');
        self::assertTrue($first->fresh()->result_published);
        self::assertFalse($first->fresh()->application_published);
        self::assertFalse($second->fresh()->result_published);
        self::assertFalse($outside->fresh()->result_published);
        self::assertSame($second->application_number, session('publicationSkipped.0.number'));
        $this->get($this->url('index'))->assertOk()->assertSee(__('scholarship.publication_skipped_details'))
            ->assertSee(__('scholarship.result_requires_complete'));
        $this->post($this->url('publication.bulk'), ['token' => $token])->assertSessionHasErrors('token');
    }

    public function test_filtered_snapshot_excludes_later_records_skips_deleted_records_and_can_unpublish(): void
    {
        $first = $this->applications->create($this->student, $this->data());
        $deleted = $this->applications->create($this->other, $this->data());
        $filters = ['branch_id' => $this->branch->id];
        $this->actingAs($this->admin);
        $preview = $this->post($this->url('publication.preview'), [
            'scope' => 'filtered', 'filters' => $filters, 'phase' => 'application', 'published' => 1,
        ])->assertOk()->assertViewHas('count', 2);
        $this->applications->delete($this->admin, $deleted->id);
        $late = $this->applications->create($this->third, $this->data());
        $this->post($this->url('publication.bulk'), ['token' => $preview->viewData('token')])
            ->assertRedirect($this->url('index', $filters));
        self::assertTrue($first->fresh()->application_published);
        self::assertFalse($late->fresh()->application_published);
        self::assertSame('pending', $first->fresh()->status);
        self::assertSame('under_review', $this->applications->forMember($this->student, $first->id)['application']['state']);
        self::assertSame(__('scholarship.application_no_longer_exists'), session('publicationSkipped.0.reason'));
        $off = $this->post($this->url('publication.preview'), [
            'scope' => 'filtered', 'filters' => $filters, 'phase' => 'application', 'published' => 0,
        ])->assertOk();
        $this->post($this->url('publication.bulk'), ['token' => $off->viewData('token')])->assertRedirect();
        self::assertFalse($first->fresh()->application_published);
    }

    public function test_preview_rejects_invalid_selection_wrong_actor_and_expired_or_wrong_token(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $index = $this->url('index');
        $this->actingAs($this->admin)->from($index);
        $data = ['scope' => 'selected', 'phase' => 'application', 'published' => 1];
        $this->post($this->url('publication.preview'), $data)->assertSessionHasErrors('ids');
        $data['ids'] = [$application->id];
        $this->post($this->url('publication.preview'), [...$data, 'filters' => ['branch_id' => $this->otherBranch->id]])
            ->assertSessionHasErrors('ids');
        $preview = $this->post($this->url('publication.preview'), $data)->assertOk();
        $token = $preview->viewData('token');
        $this->post($this->url('publication.bulk'), ['token' => str_repeat('x', 40)])->assertSessionHasErrors('token');
        $this->other->forceFill(['type' => 'admin'])->save();
        $this->actingAs($this->other)->post($this->url('publication.bulk'), ['token' => $token])->assertSessionHasErrors('token');
        $this->actingAs($this->admin);
        session()->put('scholarship_bulk_publication.expires_at', now()->subMinute()->timestamp);
        $this->post($this->url('publication.bulk'), ['token' => $token])->assertSessionHasErrors('token');
        self::assertFalse($application->fresh()->application_published);
    }

    private function url(string $action, array $parameters = []): string
    {
        return route('admin.scholarship.applications.'.$action, $parameters);
    }
}
