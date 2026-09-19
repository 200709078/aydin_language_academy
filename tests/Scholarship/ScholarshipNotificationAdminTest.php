<?php

namespace Tests\Scholarship;

use App\Jobs\SendScholarshipNotification;
use App\Models\ScholarshipApplication;
use App\Models\ScholarshipNotification;
use App\Services\ScholarshipNotificationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Mail\Transport\ArrayTransport;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ViewErrorBag;
use RuntimeException;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\RawMessage;

class ScholarshipNotificationAdminTest extends ScholarshipTestCase
{
    use InteractsWithAuthentication;
    use InteractsWithSession;
    use MakesHttpRequests;

    protected Application $app;

    private ScholarshipNotificationService $notifications;

    private mixed $bus;

    private string $locale;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = app();
        $this->notifications = app(ScholarshipNotificationService::class);
        $this->bus = Bus::getFacadeRoot();
        $this->locale = $this->app->getLocale();
        $this->app['auth']->forgetGuards();
        $this->flushSession();
        $this->withSession(['locale' => 'tr']);
        $this->app->setLocale('tr');
        $this->app['view']->share('errors', new ViewErrorBag);
        Mail::purge();
        self::assertSame('array', config('mail.default'));
    }

    protected function tearDown(): void
    {
        Bus::swap($this->bus);
        Mail::purge();
        $this->app['auth']->forgetGuards();
        $this->flushSession();
        $this->app->setLocale($this->locale);
        $this->app['view']->share('errors', new ViewErrorBag);
        parent::tearDown();
    }

    public function test_contact_and_delivery_routes_require_admin_and_contact_writes_are_independent(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $contact = $this->url('contact.update', ['application' => $application->id, 'phase' => 'application']);
        $routes = [['PATCH', $contact], ['POST', $this->url('notification.preview')], ['POST', $this->url('notification.send')]];
        foreach ($routes as [$method, $url]) {
            $this->call($method, $url)->assertRedirect(route('login'));
        }
        $this->actingAs($this->student);
        foreach ($routes as [$method, $url]) {
            $this->call($method, $url)->assertForbidden();
        }
        $show = $this->url('show', ['application' => $application->id]);
        $this->actingAs($this->admin)->get($show)->assertOk()->assertSee($contact)->assertSee(__('scholarship.delivery_empty'));
        $this->from($show)->patch($contact, ['reached' => 1, 'score' => 99, 'status' => 'approved', 'result_contact_status' => 'reached'])
            ->assertRedirect($show)->assertSessionHasNoErrors();
        $this->patch($contact, ['reached' => 1])->assertSessionHasNoErrors();
        $fresh = $application->fresh();
        self::assertSame(['reached', 'unreached', 'pending', null], [$fresh->application_contact_status, $fresh->result_contact_status, $fresh->status, $fresh->score]);
        $this->patch($contact, [])->assertSessionHasErrors('reached');
        $this->patch($this->url('contact.update', ['application' => $application->id, 'phase' => 'other']), ['reached' => 1])->assertNotFound();
        self::assertSame(0, ScholarshipNotification::query()->count());
    }

    public function test_bulk_preview_freezes_scope_rechecks_contact_and_prevents_replay(): void
    {
        Bus::fake([SendScholarshipNotification::class]);
        $first = $this->ready();
        $second = $this->applications->create($this->other, $this->data());
        $this->applications->approve($this->admin, $second->id);
        $this->applications->publish($this->admin, [$second->id], 'application');
        self::assertSame(0, ScholarshipNotification::query()->count());
        $index = $this->url('index');
        $this->actingAs($this->admin)->get($index)->assertOk()->assertSee($this->url('notification.preview'));
        $input = ['scope' => 'filtered', 'filters' => ['period_id' => $this->period->id], 'phase' => 'application', 'channel' => 'email'];
        $preview = $this->post($this->url('notification.preview'), $input)->assertOk()->assertViewHas('count', 2)
            ->assertSee($this->student->email)->assertSee('30 dakika')->assertSee('data-action-confirmation', false);
        $this->applications->markContact($this->admin, $second->id, 'application', true);
        $late = $this->applications->create($this->third, $this->data($this->alternative));
        $token = $preview->viewData('token');
        $this->post($this->url('notification.send'), ['token' => str_repeat('x', 40)])->assertSessionHasErrors('token');
        $state = session('scholarship_bulk_notification');
        $this->other->forceFill(['type' => 'admin'])->save();
        $this->actingAs($this->other)->post($this->url('notification.send'), ['token' => $token])->assertSessionHasErrors('token');
        $this->actingAs($this->admin);
        session()->put('scholarship_bulk_notification.expires_at', now()->subMinute()->timestamp);
        $this->post($this->url('notification.send'), ['token' => $token])->assertSessionHasErrors('token');
        session()->put('scholarship_bulk_notification', $state);
        session()->forget('errors');
        $this->post($this->url('notification.send'), ['token' => $token, 'ids' => [$late->id], 'phase' => 'result', 'channel' => 'whatsapp'])
            ->assertRedirect($this->url('index', $input['filters']))->assertSessionHasNoErrors();
        self::assertSame([$first->id], ScholarshipNotification::query()->pluck('application_id')->all());
        self::assertSame($second->application_number, session('notificationSkipped.0.number'));
        Bus::assertDispatchedTimes(SendScholarshipNotification::class, 1);
        $this->post($this->url('notification.send'), ['token' => $token])->assertSessionHasErrors('token');
        $single = $this->post($this->url('notification.preview'), ['scope' => 'selected', 'ids' => [$first->id], 'phase' => 'application', 'channel' => 'email'])->assertOk();
        $this->post($this->url('notification.send'), ['token' => $single->viewData('token')])->assertRedirect();
        Bus::assertDispatchedTimes(SendScholarshipNotification::class, 1);
        $this->get($this->url('show', ['application' => $first->id]))->assertOk()->assertSee(__('scholarship.delivery_status_queued'));
    }

    public function test_delivery_requires_published_data_and_keeps_sent_separate_from_contact(): void
    {
        $application = $this->applications->create($this->student, $this->data());
        $send = fn (string $phase = 'application') => $this->notifications->enqueue($this->admin, $application->id, $phase, 'email');
        $this->rejects(fn () => $this->notifications->enqueue($this->student, $application->id, 'application', 'email'), AuthorizationException::class);
        $this->rejects($send);
        $this->applications->approve($this->admin, $application->id);
        $this->rejects($send);
        $this->applications->publish($this->admin, [$application->id], 'application');
        self::assertSame('queued', $send()); // The isolated test uses the synchronous queue and array mail transport.
        $delivery = ScholarshipNotification::query()->sole();
        self::assertSame('sent', $delivery->status);
        self::assertSame(1, $delivery->attempts);
        self::assertNotNull($delivery->provider_message_id);
        self::assertSame('unreached', $application->fresh()->application_contact_status);
        self::assertSame('duplicate', $send());
        $this->notifications->deliver($delivery->id);
        self::assertCount(1, Mail::mailer()->getSymfonyTransport()->messages());
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->rejects($send);
        $this->applications->updateResult($this->admin, $application->id, ['score' => 0, 'scholarship_percentage' => 0]);
        $this->rejects(fn () => $send('result'));
        $this->applications->publish($this->admin, [$application->id], 'result');
        self::assertSame('queued', $send('result'));
        self::assertCount(2, Mail::mailer()->getSymfonyTransport()->messages());
        self::assertStringContainsString('Burs Oranı: %0', ScholarshipNotification::query()->where('phase', 'result')->sole()->body);
        $this->applications->updateResult($this->admin, $application->id, ['score' => 80]);
        self::assertSame('queued', $send('result'));
        $this->applications->updateResult($this->admin, $application->id, ['score' => 0]);
        self::assertSame('queued', $send('result'));
        self::assertCount(4, Mail::mailer()->getSymfonyTransport()->messages());
        self::assertSame('sent', $delivery->fresh()->status);
        self::assertSame('reached', $application->fresh()->application_contact_status);
        $this->rejects(fn () => $this->notifications->enqueue($this->admin, $application->id, 'result', 'whatsapp'));
        config(['mail.default' => 'log']);
        try {
            $this->rejects(fn () => $send('result'));
        } finally {
            config(['mail.default' => 'array']);
        }
    }

    public function test_worker_rechecks_publication_recipient_reached_and_content_before_sending(): void
    {
        Bus::fake([SendScholarshipNotification::class]);
        $application = $this->ready();
        $send = fn () => $this->notifications->enqueue($this->admin, $application->id, 'application', 'email');
        $send();
        $old = ScholarshipNotification::query()->sole();
        $this->applications->publish($this->admin, [$application->id], 'application', false);
        $this->notifications->deliver($old->id);
        self::assertSame(['failed', 0], [$old->fresh()->status, $old->fresh()->attempts]);
        $this->applications->publish($this->admin, [$application->id], 'application');
        $send();
        $this->student->forceFill(['email' => 'changed@example.invalid'])->save();
        $this->notifications->deliver($old->id);
        self::assertSame('failed', $old->fresh()->status);
        $send();
        $new = ScholarshipNotification::query()->latest('id')->firstOrFail();
        self::assertSame('changed@example.invalid', $new->recipient);
        $this->applications->markContact($this->admin, $application->id, 'application', true);
        $this->notifications->deliver($new->id);
        self::assertSame('failed', $new->fresh()->status);
        $this->applications->markContact($this->admin, $application->id, 'application', false);
        $send();
        $this->applications->update($this->admin, $application->id, ['session_id' => $this->alternative->id]);
        $this->notifications->deliver($new->id);
        self::assertSame('failed', $new->fresh()->status);
        self::assertCount(0, Mail::mailer()->getSymfonyTransport()->messages());
        $send();
        $latest = ScholarshipNotification::query()->latest('id')->firstOrFail();
        $this->notifications->deliver($latest->id);
        self::assertSame('sent', $latest->fresh()->status);
        $this->student->delete();
        $this->rejects($send);
    }

    public function test_latest_message_supersedes_waiting_content_even_when_details_return_to_old_values(): void
    {
        Bus::fake([SendScholarshipNotification::class]);
        $application = $this->ready();
        foreach ([$this->session, $this->alternative, $this->session] as $session) {
            $this->applications->update($this->admin, $application->id, ['session_id' => $session->id]);
            $this->notifications->enqueue($this->admin, $application->id, 'application', 'email');
        }
        $records = ScholarshipNotification::query()->orderBy('id')->get();
        self::assertCount(3, $records);
        foreach ($records as $record) {
            $this->notifications->deliver($record->id);
        }
        self::assertSame(['failed', 'failed', 'sent'], ScholarshipNotification::query()->orderBy('id')->pluck('status')->all());
        self::assertCount(1, Mail::mailer()->getSymfonyTransport()->messages());
    }

    public function test_pre_send_failures_can_retry_but_ambiguous_provider_acceptance_cannot_send_twice(): void
    {
        $application = $this->ready();
        $send = fn () => $this->notifications->enqueue($this->admin, $application->id, 'application', 'email');
        Bus::shouldReceive('dispatch')->once()->andThrow(new RuntimeException('sensitive provider error'));
        $this->rejects($send);
        $record = ScholarshipNotification::query()->sole();
        self::assertSame(['failed', 0, 'delivery_queue_failed'], [$record->status, $record->attempts, $record->failure_reason]);
        Bus::swap($this->bus);
        $transport = new class extends ArrayTransport
        {
            public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
            {
                parent::send($message, $envelope);
                throw new RuntimeException('secret provider data must not be stored');
            }
        };
        Mail::mailer()->setSymfonyTransport($transport);
        $send();
        self::assertSame(1, ScholarshipNotification::query()->count());
        self::assertSame(['failed', 1, 'delivery_uncertain'], [$record->fresh()->status, $record->fresh()->attempts, $record->fresh()->failure_reason]);
        $this->rejects($send);
        $this->notifications->deliver($record->id);
        self::assertCount(1, $transport->messages());
        self::assertSame('unreached', $application->fresh()->application_contact_status);
    }

    private function ready(): ScholarshipApplication
    {
        $application = $this->applications->create($this->student, $this->data());
        $this->applications->approve($this->admin, $application->id);
        $this->applications->publish($this->admin, [$application->id], 'application');

        return $application->fresh();
    }

    private function url(string $action, array $parameters = []): string
    {
        return route('admin.scholarship.applications.'.$action, $parameters);
    }
}
