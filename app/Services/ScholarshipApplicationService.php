<?php

namespace App\Services;

use App\Models\ScholarshipApplication;
use App\Models\ScholarshipBranch;
use App\Models\ScholarshipExamGroup;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipExamSession;
use App\Models\ScholarshipSchool;
use App\Models\ScholarshipStudentLevel;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Controllers, Livewire and future imports must use these commands for writes. */
class ScholarshipApplicationService
{
    public function create(User $actor, array $data): ScholarshipApplication
    {
        $actor = ScholarshipRules::actor($actor);
        $data = ScholarshipRules::validate($data, [
            'period_id' => ['required', ScholarshipRules::integer(), 'integer', 'min:1'],
            ...$this->studentRules(true),
        ]);

        return ScholarshipRules::transaction(function () use ($actor, $data): ScholarshipApplication {
            $period = ScholarshipRules::period((int) $data['period_id']);
            $session = $this->session($period, (int) $data['session_id']);
            $this->available($period, $session);

            if (ScholarshipApplication::query()->where('period_id', $period->id)->where('user_id', $actor->id)->lockForUpdate()->first(['id']) !== null) {
                ScholarshipRules::fail('period_id', 'Bu dönem için mevcut bir başvurunuz bulunuyor.');
            }
            $this->hasSeat($session);
            $snapshots = $this->studentData($data, null, true);

            return ScholarshipApplication::query()->create([
                ...$snapshots,
                'student_name_snapshot' => trim($data['student_name'] ?? $actor->name),
                'application_number' => (string) Str::ulid(),
                'user_id' => $actor->id,
                'period_id' => $period->id,
                'session_id' => $session->id,
            ])->refresh();
        });
    }

    public function update(User $actor, int $applicationId, array $data): ScholarshipApplication
    {
        $actor = ScholarshipRules::actor($actor);
        $data = ScholarshipRules::validate($data, $this->studentRules(false));

        return ScholarshipRules::application($applicationId, function (ScholarshipApplication $application, ScholarshipExamPeriod $period) use ($actor, $data): ScholarshipApplication {
            $admin = $actor->type === 'admin';
            $current = $this->session($period, $application->session_id);
            if (! $admin) {
                ScholarshipRules::owns($actor, $application);
                ScholarshipRules::memberWritable($application, $period, $current);
            }

            $target = $this->session($period, (int) ($data['session_id'] ?? $current->id));
            if (! $admin) {
                $this->available($period, $target);
            }
            $this->hasSeat($target, $application->id);
            $application->fill([
                ...$this->studentData($data, $application, ! $admin),
                'session_id' => $target->id,
            ]);
            if ($application->isDirty()) {
                $application->application_contact_status = 'unreached';
                $application->save();
            }

            return $application->refresh();
        });
    }

    public function delete(User $actor, int $applicationId): void
    {
        $actor = ScholarshipRules::actor($actor);
        ScholarshipRules::application($applicationId, function (ScholarshipApplication $application, ScholarshipExamPeriod $period) use ($actor): void {
            if ($actor->type !== 'admin') {
                ScholarshipRules::owns($actor, $application);
                ScholarshipRules::memberWritable($application, $period, $this->session($period, $application->session_id));
            }
            // Hard delete frees a seat and the account's period uniqueness slot.
            $application->delete();
        });
    }

    public function approve(User $admin, int $applicationId): ScholarshipApplication
    {
        ScholarshipRules::actor($admin, true);

        return ScholarshipRules::application($applicationId, function (ScholarshipApplication $application): ScholarshipApplication {
            if ($application->status !== 'approved') {
                $application->fill(['status' => 'approved', 'application_contact_status' => 'unreached'])->save();
            }

            return $application;
        });
    }

    public function updateResult(User $admin, int $applicationId, array $data): ScholarshipApplication
    {
        ScholarshipRules::actor($admin, true);
        $data = ScholarshipRules::validate($data, [
            'attendance_status' => ['sometimes', 'required', Rule::in(['unmarked', 'attended', 'absent'])],
            'score' => ['sometimes', 'nullable', ScholarshipRules::integer(), 'integer', 'between:0,100'],
            'scholarship_percentage' => ['sometimes', 'nullable', ScholarshipRules::integer(), 'integer', Rule::in(range(0, 100, 10))],
        ]);

        return ScholarshipRules::application($applicationId, function (ScholarshipApplication $application) use ($data): ScholarshipApplication {
            $attendance = $data['attendance_status'] ?? $application->attendance_status;
            if ($attendance === 'absent') {
                foreach (['score', 'scholarship_percentage'] as $field) {
                    if (isset($data[$field]) && (int) $data[$field] !== 0) {
                        ScholarshipRules::fail($field, 'Katılmayan öğrenci için not ve burs sıfır olmalıdır.');
                    }
                    $data[$field] = 0;
                }
            } elseif ($application->attendance_status === 'absent') {
                // Absence's automatic zeros are not an entered exam result.
                $data += ['score' => null, 'scholarship_percentage' => null];
            }
            $application->fill($data);
            $this->completePublishedResult($application);

            if ($application->isDirty(['attendance_status', 'score', 'scholarship_percentage'])) {
                $application->result_contact_status = 'unreached';
                $application->save();
            }

            return $application;
        });
    }

    /** @return array{updated: list<int>, skipped: array<int, array>} */
    public function publish(User $admin, array $ids, string $phase, bool $published = true): array
    {
        ScholarshipRules::actor($admin, true);
        ScholarshipRules::phase($phase);
        ScholarshipRules::validate(['ids' => $ids], ['ids' => ['required', 'array', 'max:1000'], 'ids.*' => ['required', ScholarshipRules::integer(), 'integer', 'min:1']]);
        $result = ['updated' => [], 'skipped' => []];

        foreach (array_unique(array_map('intval', $ids)) as $id) {
            try {
                ScholarshipRules::application($id, function (ScholarshipApplication $application) use ($phase, $published): void {
                    $application->setAttribute($phase.'_published', $published);
                    $this->completePublishedResult($application);
                    $application->save();
                });
                $result['updated'][] = $id;
            } catch (ValidationException $e) {
                $result['skipped'][$id] = $e->errors();
            } catch (ModelNotFoundException) {
                $result['skipped'][$id] = ['application' => ['Başvuru artık mevcut değil.']];
            }
        }

        return $result;
    }

    public function markContact(User $admin, int $applicationId, string $phase, bool $reached): ScholarshipApplication
    {
        ScholarshipRules::actor($admin, true);
        ScholarshipRules::phase($phase);

        return ScholarshipRules::application($applicationId, function (ScholarshipApplication $application) use ($phase, $reached): ScholarshipApplication {
            $application->setAttribute($phase.'_contact_status', $reached ? 'reached' : 'unreached');
            $application->save();

            return $application;
        });
    }

    /** Allowlisted member output; never serialize the administrative model directly. */
    public function forMember(User $user, int $applicationId): array
    {
        $user = ScholarshipRules::actor($user);
        $application = ScholarshipApplication::query()->findOrFail($applicationId);
        ScholarshipRules::owns($user, $application);

        return $this->memberData($application);
    }

    public function listForMember(User $user): Collection
    {
        $user = ScholarshipRules::actor($user);

        return ScholarshipApplication::query()->where('user_id', $user->id)
            ->with(['period', 'session.branch', 'session.examGroup'])
            ->orderByDesc('id')->get()->map(fn ($application) => $this->memberData($application));
    }

    private function memberData(ScholarshipApplication $application): array
    {
        $application->loadMissing(['period', 'session.branch', 'session.examGroup']);
        $session = $application->session;
        $publishedApproval = $application->application_published && $application->status === 'approved';
        $resultVisible = $application->result_published && $application->score !== null && $application->scholarship_percentage !== null;
        $block = ScholarshipRules::memberBlock($application, $application->period, $session);

        return [
            'id' => $application->id,
            'application_number' => $application->application_number,
            'student' => ['name' => $application->student_name_snapshot, 'school' => $application->school_name_snapshot, 'level' => $application->student_level_name_snapshot],
            'period' => ['id' => $application->period_id, 'title' => $application->period->title],
            'session' => [
                'id' => $session->id, 'branch' => $session->branch->name, 'address' => $session->branch->address,
                'exam_group' => $session->examGroup->name, 'exam_title' => $session->exam_title,
                'date' => $session->exam_date->format('Y-m-d'), 'starts_at' => $session->starts_at, 'ends_at' => $session->ends_at,
                'state' => $session->archived_at !== null ? 'archived' : ($session->is_active ? 'active' : 'suspended'),
            ],
            'application' => [
                'published' => $application->application_published,
                'state' => $publishedApproval ? 'approved' : 'under_review',
                'arrival_at' => $publishedApproval ? $session->startsAt()->subMinutes(30)->format('Y-m-d H:i:s') : null,
            ],
            'result' => $resultVisible
                ? ['published' => true, 'attendance_status' => $application->attendance_status, 'score' => $application->score, 'scholarship_percentage' => $application->scholarship_percentage]
                : ['published' => false],
            'can_edit' => $block === null,
            'can_delete' => $block === null,
            'restriction' => $block,
        ];
    }

    private function studentRules(bool $creating): array
    {
        $presence = $creating ? 'required' : 'sometimes';

        return [
            'session_id' => [$presence, 'required', ScholarshipRules::integer(), 'integer', 'min:1'],
            'school_id' => [$presence, 'required', ScholarshipRules::integer(), 'integer', 'min:1'],
            'student_level_id' => [$presence, 'required', ScholarshipRules::integer(), 'integer', 'min:1'],
            'student_name' => ['sometimes', 'required', 'string', 'max:255', 'not_regex:/^\s*$/u'],
        ];
    }

    private function studentData(array $data, ?ScholarshipApplication $application, bool $requireActive): array
    {
        $attributes = [];
        foreach (['school_id' => [ScholarshipSchool::class, 'school_name_snapshot'], 'student_level_id' => [ScholarshipStudentLevel::class, 'student_level_name_snapshot']] as $field => [$model, $snapshot]) {
            if (! array_key_exists($field, $data) || ($application !== null && (int) $data[$field] === (int) $application->$field)) {
                continue;
            }
            $definition = $model::query()->lockForUpdate()->find($data[$field]);
            if ($definition === null || ($requireActive && ! $definition->is_active)) {
                ScholarshipRules::fail($field, 'Seçilen kayıt başvuruya uygun değil.');
            }
            $attributes[$field] = $definition->id;
            $attributes[$snapshot] = $definition->name;
        }
        if (isset($data['student_name'])) {
            $attributes['student_name_snapshot'] = trim($data['student_name']);
        }

        return $attributes;
    }

    private function session(ScholarshipExamPeriod $period, int $sessionId): ScholarshipExamSession
    {
        $session = ScholarshipExamSession::query()->where('period_id', $period->id)->lockForUpdate()->find($sessionId);
        if ($session === null) {
            ScholarshipRules::fail('session_id', 'Oturum seçilen döneme ait değil.');
        }

        return $session;
    }

    private function available(ScholarshipExamPeriod $period, ScholarshipExamSession $session): void
    {
        if (! $period->acceptsApplications()) {
            ScholarshipRules::fail('period_id', 'Bu dönemin başvuruları şu anda kapalı.');
        }
        $branch = ScholarshipBranch::query()->lockForUpdate()->findOrFail($session->branch_id);
        $group = ScholarshipExamGroup::query()->lockForUpdate()->findOrFail($session->exam_group_id);
        if (! $session->is_active || $session->archived_at !== null || ! $branch->is_active || ! $group->is_active || $session->startsAt()->lte(now())) {
            ScholarshipRules::fail('session_id', 'Bu oturum başvuruya uygun değil.');
        }
    }

    private function hasSeat(ScholarshipExamSession $session, ?int $exceptApplication = null): void
    {
        $occupied = ScholarshipApplication::query()->where('session_id', $session->id)
            ->when($exceptApplication !== null, fn ($query) => $query->where('id', '!=', $exceptApplication))
            ->lockForUpdate()->get(['id'])->count();
        if ($occupied >= $session->capacity) {
            ScholarshipRules::fail('session_id', 'Bu oturumun kontenjanı dolu.');
        }
    }

    private function completePublishedResult(ScholarshipApplication $application): void
    {
        if ($application->result_published && ($application->score === null || $application->scholarship_percentage === null)) {
            ScholarshipRules::fail('result_published', 'Not ve burs girilmeden sonuç yayınlanamaz. Eksik sonuç kaydetmek için önce yayını kapatın.');
        }
    }
}
