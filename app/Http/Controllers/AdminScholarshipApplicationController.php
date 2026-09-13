<?php

namespace App\Http\Controllers;

use App\Models\ScholarshipApplication;
use App\Models\ScholarshipBranch;
use App\Models\ScholarshipExamGroup;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipExamSession;
use App\Models\ScholarshipSchool;
use App\Models\ScholarshipStudentLevel;
use App\Services\ScholarshipApplicationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminScholarshipApplicationController extends Controller
{
    public function __construct(private readonly ScholarshipApplicationService $applications) {}

    public function index(Request $request): View
    {
        $filters = $this->filters($request->query());
        $query = $this->query($filters);

        return view('admin.scholarship.applications.index', [
            ...$this->choices(),
            'filters' => $filters,
            'pendingCount' => (clone $query)->where('status', 'pending')->count(),
            'applications' => $query->with(['user', 'period', 'session.branch', 'session.examGroup'])
                ->latest('id')->paginate(20)->withQueryString(),
        ]);
    }

    public function show(ScholarshipApplication $application): View
    {
        $application->load(['user', 'period', 'session.branch', 'session.examGroup']);

        return view('admin.scholarship.applications.show', compact('application'));
    }

    public function edit(ScholarshipApplication $application): View
    {
        $application->load(['user', 'period', 'session.branch', 'session.examGroup']);

        return view('admin.scholarship.applications.edit', [
            'application' => $application,
            'schools' => ScholarshipSchool::query()->orderBy('sort_order')->orderBy('name')->get(),
            'levels' => ScholarshipStudentLevel::query()->orderBy('sort_order')->orderBy('name')->get(),
            'sessions' => ScholarshipExamSession::query()->where('period_id', $application->period_id)
                ->with(['branch', 'examGroup'])->withCount('applications')
                ->orderBy('exam_date')->orderBy('starts_at')->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, ScholarshipApplication $application): RedirectResponse
    {
        // Require the complete form, and never pass status, owner or result fields to the service.
        $data = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'school_id' => ['required', 'integer', 'min:1'],
            'student_level_id' => ['required', 'integer', 'min:1'],
            'session_id' => ['required', 'integer', 'min:1'],
        ]);
        $this->applications->update($request->user(), $application->id, $data);

        return $this->success('application_updated', $application);
    }

    public function approve(Request $request, ScholarshipApplication $application): RedirectResponse
    {
        $this->applications->approve($request->user(), $application->id);

        return $this->success('application_approved', $application);
    }

    public function destroy(Request $request, ScholarshipApplication $application): RedirectResponse
    {
        $this->applications->delete($request->user(), $application->id);

        return $this->success('application_deleted');
    }

    public function previewApproval(Request $request): View
    {
        $data = $request->validate([
            'scope' => ['required', Rule::in(['selected', 'filtered'])],
            'ids' => ['required_if:scope,selected', 'array', 'max:1000'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
            'filters' => ['sometimes', 'array'],
        ], ['ids.required_if' => __('scholarship.no_pending_selection')], ['ids' => __('scholarship.scope_selected')]);
        $filters = $this->filters($data['filters'] ?? []);
        $query = $this->query($filters);
        if ($data['scope'] === 'selected') {
            $query->whereIn('id', $data['ids']);
            if ((clone $query)->count() !== count($data['ids'])) {
                throw ValidationException::withMessages(['ids' => __('scholarship.selection_changed')]);
            }
        }
        $ids = $query->where('status', 'pending')->orderBy('id')->pluck('id')->all();
        if ($ids === []) {
            throw ValidationException::withMessages(['ids' => __('scholarship.no_pending_selection')]);
        }
        $token = Str::random(40);
        // Temporary confirmation state, not an audit record. New matching rows are excluded.
        $request->session()->put('scholarship_bulk_approval', [
            'token' => $token, 'ids' => $ids, 'actor_id' => $request->user()->id,
            'expires_at' => now()->addMinutes(30)->timestamp,
        ]);

        return view('admin.scholarship.applications.approval-preview', [
            'token' => $token, 'count' => count($ids), 'scope' => $data['scope'], 'filters' => $filters,
            'applications' => ScholarshipApplication::query()->with(['period', 'session.branch', 'session.examGroup'])
                ->whereIn('id', array_slice($ids, 0, 20))->orderBy('id')->get(),
        ]);
    }

    public function approveBulk(Request $request): RedirectResponse
    {
        $data = $request->validate(['token' => ['required', 'string', 'size:40']], [], ['token' => __('scholarship.approval_preview')]);
        $preview = $request->session()->get('scholarship_bulk_approval');
        if (! is_array($preview) || ! hash_equals($preview['token'], $data['token'])
            || $preview['actor_id'] !== $request->user()->id || $preview['expires_at'] < now()->timestamp) {
            throw ValidationException::withMessages(['token' => __('scholarship.approval_preview_expired')]);
        }
        $request->session()->forget('scholarship_bulk_approval');
        $approved = 0;
        $skipped = 0;
        foreach ($preview['ids'] as $id) {
            try {
                $this->applications->approve($request->user(), $id);
                $approved++;
            } catch (ModelNotFoundException|ValidationException) {
                $skipped++;
            }
        }

        return redirect()->route('admin.scholarship.applications.index')
            ->with('modalSuccessTitle', __('scholarship.applications'))
            ->with('modalSuccessContent', __('scholarship.bulk_approval_completed', compact('approved', 'skipped')));
    }

    private function filters(array $input): array
    {
        $rules = ['q' => ['nullable', 'string', 'max:255']];
        foreach (['period_id' => 'exam_periods', 'branch_id' => 'branches', 'exam_group_id' => 'exam_groups',
            'session_id' => 'exam_sessions', 'school_id' => 'schools', 'student_level_id' => 'student_levels'] as $field => $table) {
            $rules[$field] = ['nullable', 'integer', 'exists:scholarship_'.$table.',id'];
        }
        foreach (['status' => ['pending', 'approved'], 'attendance_status' => ['unmarked', 'attended', 'absent'],
            'application_contact_status' => ['unreached', 'reached'], 'result_contact_status' => ['unreached', 'reached'],
            'session_state' => ['active', 'suspended', 'archived']] as $field => $values) {
            $rules[$field] = ['nullable', Rule::in($values)];
        }
        $rules['scholarship_percentage'] = ['nullable', Rule::in(['unset', ...range(0, 100, 10)])];
        foreach (['application_published', 'result_published'] as $field) {
            $rules[$field] = ['nullable', 'boolean'];
        }
        $rules['exam_date'] = ['nullable', 'date_format:Y-m-d'];
        foreach (['starts_at', 'ends_at'] as $field) {
            $rules[$field] = ['nullable', 'date_format:H:i:s,H:i'];
        }

        return array_filter(Validator::make($input, $rules)->validate(), fn ($value) => $value !== null && $value !== '');
    }

    private function query(array $filters): Builder
    {
        $query = ScholarshipApplication::query();
        foreach (['period_id', 'session_id', 'school_id', 'student_level_id', 'status', 'attendance_status',
            'application_contact_status', 'result_contact_status', 'application_published', 'result_published'] as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (isset($filters['scholarship_percentage'])) {
            $filters['scholarship_percentage'] === 'unset'
                ? $query->whereNull('scholarship_percentage')
                : $query->where('scholarship_percentage', $filters['scholarship_percentage']);
        }
        $query->whereHas('session', function (Builder $sessions) use ($filters): void {
            foreach (['branch_id', 'exam_group_id', 'exam_date', 'starts_at', 'ends_at'] as $field) {
                if (isset($filters[$field])) {
                    $sessions->where($field, $filters[$field]);
                }
            }
            if (($filters['session_state'] ?? null) === 'archived') {
                $sessions->whereNotNull('archived_at');
            } elseif (isset($filters['session_state'])) {
                $sessions->whereNull('archived_at')->where('is_active', $filters['session_state'] === 'active');
            }
        });
        if (isset($filters['q'])) {
            $search = '%'.trim($filters['q']).'%';
            $query->where(fn (Builder $names) => $names->where('application_number', 'like', $search)
                ->orWhere('student_name_snapshot', 'like', $search)
                ->orWhereHas('user', fn (Builder $users) => $users->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)->orWhere('phone', 'like', $search)));
        }

        return $query;
    }

    private function choices(): array
    {
        return [
            'periods' => ScholarshipExamPeriod::query()->latest('id')->get(),
            'branches' => ScholarshipBranch::query()->orderBy('sort_order')->orderBy('name')->get(),
            'groups' => ScholarshipExamGroup::query()->orderBy('sort_order')->orderBy('name')->get(),
            'schools' => ScholarshipSchool::query()->orderBy('sort_order')->orderBy('name')->get(),
            'levels' => ScholarshipStudentLevel::query()->orderBy('sort_order')->orderBy('name')->get(),
            'sessions' => ScholarshipExamSession::query()->with(['branch', 'examGroup'])->orderByDesc('exam_date')->orderBy('starts_at')->get(),
        ];
    }

    private function success(string $message, ?ScholarshipApplication $application = null): RedirectResponse
    {
        return ($application === null ? redirect()->route('admin.scholarship.applications.index')
            : redirect()->route('admin.scholarship.applications.show', $application))
            ->with('modalSuccessTitle', __('scholarship.applications'))
            ->with('modalSuccessContent', __('scholarship.'.$message));
    }
}
