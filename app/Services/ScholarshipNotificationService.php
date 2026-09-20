<?php

namespace App\Services;

use App\Jobs\SendScholarshipNotification;
use App\Models\ScholarshipApplication;
use App\Models\ScholarshipNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ScholarshipNotificationService
{
    /** A logical message keeps its identity across repeated clicks and safe retries. */
    public function enqueue(User $admin, int $applicationId, string $phase, string $channel): string
    {
        ScholarshipRules::actor($admin, true);
        ScholarshipRules::phase($phase);
        $this->assertChannel($channel);

        [$notification, $dispatch] = ScholarshipRules::application($applicationId, function (ScholarshipApplication $application) use ($phase, $channel): array {
            $message = $this->message($application, $phase, $channel);
            $history = ScholarshipNotification::query()->where('application_id', $application->id)->where('phase', $phase)->where('channel', $channel);
            $notification = (clone $history)->latest('id')->first();
            if ($notification !== null && $message === $notification->only(['recipient', 'subject', 'body'])) {
                if ($notification->status === 'failed' && $notification->attempts > 0) {
                    ScholarshipRules::fail('notification', __('scholarship.delivery_uncertain'));
                }
                if ($notification->status !== 'failed') {
                    return [$notification, false];
                }
                // Only failures BEFORE calling the provider can be safely retried.
                $notification->forceFill(['status' => 'queued', 'queued_at' => now(), 'failed_at' => null, 'failure_reason' => null])->save();
            } else {
                // A -> B -> A is a new communication, not a duplicate of historical A.
                // Cancel older unclaimed messages so they cannot become valid again.
                (clone $history)->where('status', 'queued')->where('attempts', 0)->update([
                    'status' => 'failed', 'failed_at' => now(), 'failure_reason' => 'delivery_changed',
                ]);
                $notification = ScholarshipNotification::query()->create([
                    'application_id' => $application->id, 'phase' => $phase, 'channel' => $channel,
                    'idempotency_key' => (string) Str::uuid(), ...$message, 'queued_at' => now(),
                ]);
            }

            return [$notification, true];
        });

        if (! $dispatch) {
            return 'duplicate';
        }
        try {
            Bus::dispatch(new SendScholarshipNotification($notification->id));
        } catch (Throwable) {
            ScholarshipNotification::query()->whereKey($notification->id)->where('status', 'queued')->where('attempts', 0)
                ->update(['status' => 'failed', 'failed_at' => now(), 'failure_reason' => 'delivery_queue_failed']);
            ScholarshipRules::fail('notification', __('scholarship.delivery_queue_failed'));
        }

        return 'queued';
    }

    public function message(ScholarshipApplication $application, string $phase, string $channel): array
    {
        ScholarshipRules::phase($phase);
        $this->assertChannel($channel);
        if ($application->getAttribute($phase.'_contact_status') === 'reached') {
            ScholarshipRules::fail('notification', __('scholarship.delivery_reached'));
        }
        if (($phase === 'application' && (! $application->application_published || $application->status !== 'approved'))
            || ($phase === 'result' && (! $application->result_published || $application->scholarship_percentage === null))) {
            ScholarshipRules::fail('notification', __('scholarship.delivery_not_published'));
        }
        $application->loadMissing(['user', 'period', 'session.branch', 'session.examGroup']);
        $recipient = trim((string) $application->user?->email);
        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            ScholarshipRules::fail('notification', __('scholarship.delivery_no_email'));
        }
        // Accounts currently have no saved language preference. Use the institution's
        // Turkish message consistently in HTTP previews and queue workers.
        $label = static fn (string $key): string => __('scholarship.'.$key, [], 'tr');
        $session = $application->session;
        $lines = [
            $label('student_name').': '.$application->student_name_snapshot,
            $label('application_number').': '.$application->application_number,
            $label('period').': '.$application->period->title,
        ];
        if ($phase === 'application') {
            $lines = [...$lines, $label('delivery_accepted'),
                $label('current_school').': '.$application->school_name_snapshot,
                $label('current_level').': '.$application->student_level_name_snapshot,
                $label('branch_name').': '.$session->branch->name,
                $label('definition_address').': '.($session->branch->address ?? '—'),
                $label('exam_groups').': '.$session->examGroup->name,
                $session->exam_title,
                $session->exam_date->format('d.m.Y').' '.$session->starts_at.'–'.$session->ends_at.' ('.config('app.timezone').')',
                $label('delivery_arrive_early')];
        } else {
            $lines = [...$lines, $label('attendance').': '.$label('attendance_'.$application->attendance_status),
                $label('score').': '.($application->score ?? $label('not_entered')),
                $label('scholarship_percentage').': %'.$application->scholarship_percentage];
            foreach (['correct_count', 'wrong_count', 'blank_count'] as $field) {
                $value = $application->attendance_status === 'absent'
                    ? $label('not_applicable') : ($application->$field ?? $label('not_entered'));
                $lines[] = $label($field).': '.$value;
            }
        }

        return ['recipient' => $recipient, 'subject' => $label('delivery_subject_'.$phase).' — '.$application->application_number,
            'body' => implode("\n", [...$lines, '', 'ALA'])];
    }

    public function deliver(int $notificationId): void
    {
        $record = ScholarshipNotification::query()->find($notificationId);
        if ($record === null) {
            return;
        }
        try {
            $notification = ScholarshipRules::application($record->application_id, function (ScholarshipApplication $application) use ($notificationId): ?ScholarshipNotification {
                $notification = ScholarshipNotification::query()->lockForUpdate()->find($notificationId);
                if ($notification === null || $notification->status !== 'queued' || $notification->attempts > 0) {
                    return null;
                }
                try {
                    $message = $this->message($application, $notification->phase, $notification->channel);
                    if ($message !== $notification->only(['recipient', 'subject', 'body'])) {
                        ScholarshipRules::fail('notification', __('scholarship.delivery_changed'));
                    }
                } catch (ValidationException) {
                    $notification->forceFill(['status' => 'failed', 'failed_at' => now(), 'failure_reason' => 'delivery_changed'])->save();

                    return null;
                }
                // Commit the claim BEFORE external I/O. A redelivered job must not send
                // again even if the worker dies after SMTP acceptance but before saving.
                $notification->forceFill(['attempts' => 1, 'provider' => 'mail:'.config('mail.default')])->save();

                return $notification;
            });
        } catch (ModelNotFoundException) {
            return;
        }
        if ($notification === null) {
            return;
        }

        try {
            $sent = Mail::mailer()->raw($notification->body, function ($mail) use ($notification): void {
                $mail->to($notification->recipient)->subject($notification->subject);
            });
            if ($sent === null) {
                $this->failDelivery($notificationId);

                return;
            }
            ScholarshipNotification::query()->whereKey($notificationId)->where('status', 'queued')->update([
                'status' => 'sent', 'sent_at' => now(), 'failed_at' => null, 'failure_reason' => null,
                'provider_message_id' => $sent->getMessageId(),
            ]);
        } catch (Throwable) {
            // SMTP errors can occur after acceptance. Never auto-retry an ambiguous send,
            // and never persist exception text that may contain credentials or message data.
            $this->failDelivery($notificationId);
        }
    }

    public function failDelivery(int $notificationId): void
    {
        $record = ScholarshipNotification::query()->find($notificationId);
        if ($record !== null && $record->status === 'queued') {
            ScholarshipNotification::query()->whereKey($notificationId)->where('status', 'queued')->where('attempts', $record->attempts)->update([
                'status' => 'failed', 'failed_at' => now(),
                'failure_reason' => $record->attempts > 0 ? 'delivery_uncertain' : 'delivery_queue_failed',
            ]);
        }
    }

    private function assertChannel(string $channel): void
    {
        if ($channel !== 'email') {
            ScholarshipRules::fail('channel', __('scholarship.delivery_whatsapp_pending'));
        }
        $transport = config('mail.mailers.'.config('mail.default').'.transport');
        if (! in_array($transport, ['smtp', 'sendmail', 'ses', 'ses-v2', 'postmark', 'resend'], true)
            && ! ($transport === 'array' && app()->environment('testing'))) {
            // A log/failover-to-log transport must never be reported as a real delivery.
            ScholarshipRules::fail('channel', __('scholarship.delivery_mail_not_ready'));
        }
    }
}
