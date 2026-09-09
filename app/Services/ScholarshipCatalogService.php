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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class ScholarshipCatalogService
{
    public function savePeriod(User $admin, array $data, ?int $id = null): ScholarshipExamPeriod
    {
        ScholarshipRules::actor($admin, true);

        return ScholarshipRules::transaction(function () use ($data, $id): ScholarshipExamPeriod {
            $period = $id === null ? new ScholarshipExamPeriod : ScholarshipRules::period($id);
            $rules = [
                'title' => ['required', 'string', 'max:255', 'not_regex:/^\s*$/u'],
                'description' => ['nullable', 'string'],
                'applications_open_at' => ['required', 'date_format:Y-m-d H:i:s'],
                'applications_close_at' => ['required', 'date_format:Y-m-d H:i:s', 'after:applications_open_at'],
                'exam_starts_on' => ['required', 'date_format:Y-m-d'],
                'exam_ends_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:exam_starts_on'],
                'is_active' => ['boolean'],
                'applications_open' => ['boolean'],
            ];
            $attributes = ScholarshipRules::validate($this->merge($period, $data, $rules), $rules);
            if ($period->exists && $period->sessions()->where(function ($query) use ($attributes): void {
                $query->where('exam_date', '<', $attributes['exam_starts_on'])->orWhere('exam_date', '>', $attributes['exam_ends_on']);
            })->lockForUpdate()->first(['id']) !== null) {
                ScholarshipRules::fail('exam_starts_on', __('dictt.scholarship_period_must_include_sessions'));
            }
            $period->fill($attributes);
            $communicatedChange = $period->exists && $period->isDirty(['title', 'description']);
            $period->save();
            if ($communicatedChange) {
                $period->applications()->update(['application_contact_status' => 'unreached']);
            }

            return $period->refresh();
        });
    }

    public function deletePeriod(User $admin, int $id): void
    {
        ScholarshipRules::actor($admin, true);
        ScholarshipRules::transaction(function () use ($id): void {
            $period = ScholarshipRules::period($id);
            if ($period->sessions()->lockForUpdate()->first(['id']) !== null || $period->applications()->lockForUpdate()->first(['id']) !== null) {
                ScholarshipRules::fail('period_id', __('dictt.scholarship_period_has_dependents'));
            }
            $period->delete();
        });
    }

    public function saveSession(User $admin, array $data, ?int $id = null): ScholarshipExamSession
    {
        ScholarshipRules::actor($admin, true);
        // Resolve immutable period identity outside the transaction (MySQL RR).
        $periodId = $id === null ? ($data['period_id'] ?? null) : ScholarshipExamSession::query()->findOrFail($id)->period_id;
        ScholarshipRules::validate(['period_id' => $periodId], ['period_id' => ['required', ScholarshipRules::integer(), 'integer', 'min:1']]);

        return ScholarshipRules::transaction(function () use ($data, $id, $periodId): ScholarshipExamSession {
            $period = ScholarshipRules::period((int) $periodId);
            $session = $id === null ? new ScholarshipExamSession : ScholarshipExamSession::query()->lockForUpdate()->findOrFail($id);
            $rules = [
                'period_id' => ['required', ScholarshipRules::integer(), 'integer', Rule::in([$period->id])],
                'branch_id' => ['required', ScholarshipRules::integer(), 'integer', 'min:1'],
                'exam_group_id' => ['required', ScholarshipRules::integer(), 'integer', 'min:1'],
                'exam_title' => ['required', 'string', 'max:150', 'not_regex:/^\s*$/u'],
                'exam_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$period->exam_starts_on->format('Y-m-d'), 'before_or_equal:'.$period->exam_ends_on->format('Y-m-d')],
                'starts_at' => ['required', 'date_format:H:i:s'],
                'ends_at' => ['required', 'date_format:H:i:s', 'after:starts_at'],
                'capacity' => ['required', ScholarshipRules::integer(), 'integer', 'between:1,4294967295'],
                'is_active' => ['boolean'],
            ];
            $attributes = ScholarshipRules::validate($this->merge($session, $data, $rules), $rules);
            foreach (['branch_id' => ScholarshipBranch::class, 'exam_group_id' => ScholarshipExamGroup::class] as $field => $model) {
                if (! $model::query()->lockForUpdate()->find($attributes[$field])) {
                    ScholarshipRules::fail($field, 'Seçilen tanım bulunamadı.');
                }
            }
            if ($session->exists && $session->applications()->lockForUpdate()->get(['id'])->count() > (int) $attributes['capacity']) {
                ScholarshipRules::fail('capacity', 'Kontenjan mevcut başvuru sayısının altına indirilemez.');
            }
            $definition = array_intersect_key($attributes, array_flip(['period_id', 'branch_id', 'exam_group_id', 'exam_title', 'exam_date', 'starts_at', 'ends_at']));
            if (ScholarshipExamSession::query()->where($definition)->when($id !== null, fn ($query) => $query->where('id', '!=', $id))->lockForUpdate()->first(['id']) !== null) {
                ScholarshipRules::fail('starts_at', 'Bu sınav için aynı tarih ve saatlerde bir oturum zaten var.');
            }
            $session->fill($attributes);
            $communicatedChange = $session->exists && $session->isDirty(['branch_id', 'exam_group_id', 'exam_title', 'exam_date', 'starts_at', 'ends_at']);
            $session->save();
            if ($communicatedChange) {
                $session->applications()->update(['application_contact_status' => 'unreached']);
            }

            return $session->refresh();
        });
    }

    public function archiveSession(User $admin, int $id, bool $archived = true): ScholarshipExamSession
    {
        ScholarshipRules::actor($admin, true);
        $periodId = ScholarshipExamSession::query()->findOrFail($id)->period_id;

        return ScholarshipRules::transaction(function () use ($id, $periodId, $archived): ScholarshipExamSession {
            ScholarshipRules::period($periodId);
            $session = ScholarshipExamSession::query()->lockForUpdate()->findOrFail($id);
            $session->archived_at = $archived ? ($session->archived_at ?? now()) : null;
            $session->save();

            return $session;
        });
    }

    public function deleteSession(User $admin, int $id): void
    {
        ScholarshipRules::actor($admin, true);
        $periodId = ScholarshipExamSession::query()->findOrFail($id)->period_id;
        ScholarshipRules::transaction(function () use ($id, $periodId): void {
            ScholarshipRules::period($periodId);
            $session = ScholarshipExamSession::query()->lockForUpdate()->findOrFail($id);
            if ($session->applications()->lockForUpdate()->first(['id']) !== null) {
                ScholarshipRules::fail('session_id', 'Başvurusu bulunan oturum silinemez; arşive alınabilir.');
            }
            $session->delete();
        });
    }

    public function saveDefinition(User $admin, string $type, array $data, ?int $id = null): Model
    {
        ScholarshipRules::actor($admin, true);
        $model = $this->definitionModel($type);

        return ScholarshipRules::transaction(function () use ($model, $type, $data, $id): Model {
            // Definitions span periods. Lock periods in the same order before
            // locking a shared definition, matching application/session writes.
            ScholarshipExamPeriod::query()->orderBy('id')->lockForUpdate()->get(['id']);
            $definition = $id === null ? new $model : $model::query()->lockForUpdate()->findOrFail($id);
            $rules = [
                'name' => ['required', 'string', 'max:'.match ($type) {
                    'school' => 255, 'branch' => 150, default => 100
                }, 'not_regex:/^\s*$/u'],
                'is_active' => ['boolean'],
                'sort_order' => [ScholarshipRules::integer(), 'integer', 'between:0,4294967295'],
            ];
            if ($type !== 'school') {
                $rules['code'] = ['required', 'string', 'max:64', 'not_regex:/^\s*$/u', Rule::unique($definition->getTable(), 'code')->ignore($id)];
            }
            if ($type === 'branch') {
                $rules['address'] = ['nullable', 'string'];
            }
            $definition->fill(ScholarshipRules::validate($this->merge($definition, $data, $rules), $rules));
            $communicatedChange = $definition->exists && in_array($type, ['branch', 'exam_group'], true) && $definition->isDirty(['name', 'address']);
            $definition->save();
            if ($communicatedChange) {
                $sessionField = $type === 'branch' ? 'branch_id' : 'exam_group_id';
                ScholarshipApplication::query()->whereIn('session_id', ScholarshipExamSession::query()->select('id')->where($sessionField, $definition->id))
                    ->update(['application_contact_status' => 'unreached']);
            }

            return $definition->refresh();
        });
    }

    public function deleteDefinition(User $admin, string $type, int $id): void
    {
        ScholarshipRules::actor($admin, true);
        $model = $this->definitionModel($type);
        ScholarshipRules::transaction(function () use ($model, $type, $id): void {
            ScholarshipExamPeriod::query()->orderBy('id')->lockForUpdate()->get(['id']);
            $definition = $model::query()->lockForUpdate()->findOrFail($id);
            $used = match ($type) {
                'branch', 'exam_group' => $definition->sessions()->lockForUpdate()->first(['id']) !== null,
                default => $definition->applications()->lockForUpdate()->first(['id']) !== null,
            };
            if ($used) {
                ScholarshipRules::fail('definition', 'Kullanılan tanım silinemez; pasifleştirilebilir.');
            }
            $definition->delete();
        });
    }

    private function definitionModel(string $type): string
    {
        return match ($type) {
            'branch' => ScholarshipBranch::class,
            'school' => ScholarshipSchool::class,
            'student_level' => ScholarshipStudentLevel::class,
            'exam_group' => ScholarshipExamGroup::class,
            default => throw \Illuminate\Validation\ValidationException::withMessages(['type' => 'Geçersiz tanım türü.']),
        };
    }

    /** Merge only supported current attributes; submitted unknown keys still fail. */
    private function merge(Model $model, array $data, array $rules): array
    {
        $current = array_intersect_key($model->getRawOriginal(), $rules);
        $attributes = [...$current, ...$data];
        foreach (['applications_open_at', 'applications_close_at'] as $field) {
            if (isset($attributes[$field]) && is_string($attributes[$field])) {
                $attributes[$field] = str_replace('T', ' ', $attributes[$field]);
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/D', $attributes[$field])) {
                    $attributes[$field] .= ':00';
                }
            }
        }
        foreach (['starts_at', 'ends_at'] as $field) {
            if (isset($attributes[$field]) && is_string($attributes[$field]) && preg_match('/^\d{2}:\d{2}$/D', $attributes[$field])) {
                $attributes[$field] .= ':00';
            }
        }

        return $attributes;
    }
}
