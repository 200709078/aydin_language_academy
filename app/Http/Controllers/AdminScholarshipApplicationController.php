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
use App\Services\ScholarshipNotificationService;
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
    public function __construct(
        private readonly ScholarshipApplicationService $applications,
        private readonly ScholarshipNotificationService $notifications,
    ) {}

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

        return view('admin.scholarship.applications.show', [
            'application' => $application,
            'deliveries' => $application->notifications()->latest('id')->paginate(20),
        ]);
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

    public function updatePublication(Request $request, ScholarshipApplication $application, string $phase): RedirectResponse
    {
        $data = Validator::make($request->post(), ['published' => ['required', 'boolean']], [], [
            'published' => __('scholarship.publication_management'),
        ])->validate();
        $result = $this->applications->publish($request->user(), [$application->id], $phase, (bool) $data['published']);
        if (isset($result['skipped'][$application->id])) {
            throw ValidationException::withMessages($result['skipped'][$application->id]);
        }

        return redirect()->back()->with('modalSuccessTitle', __('scholarship.publication_management'))
            ->with('modalSuccessContent', __('scholarship.publication_updated', [
                'number' => $application->application_number, 'phase' => __('scholarship.'.$phase.'_publication'),
                'state' => __('scholarship.'.($data['published'] ? 'published' : 'unpublished')),
            ]));
    }

    public function previewPublication(Request $request): View
    {
        $data = Validator::make($request->post(), [
            'scope' => ['required', Rule::in(['selected', 'filtered'])],
            'phase' => ['required', Rule::in(['application', 'result'])],
            'published' => ['required', 'boolean'],
            'ids' => ['required_if:scope,selected', 'array', 'max:1000'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
            'filters' => ['sometimes', 'array'],
        ], ['ids.required_if' => __('scholarship.publication_no_selection')], [
            'ids' => __('scholarship.scope_selected'), 'phase' => __('scholarship.publication_phase'),
            'published' => __('scholarship.publication_action'),
        ])->validate();
        $filters = $this->filters($data['filters'] ?? []);
        $query = $this->query($filters);
        if ($data['scope'] === 'selected') {
            $query->whereIn('id', $data['ids']);
            if ((clone $query)->count() !== count($data['ids'])) {
                throw ValidationException::withMessages(['ids' => __('scholarship.selection_changed')]);
            }
        }
        $ids = $query->orderBy('id')->pluck('id')->all();
        if ($ids === []) {
            throw ValidationException::withMessages(['ids' => __('scholarship.publication_no_selection')]);
        }
        $phase = $data['phase'];
        $published = (bool) $data['published'];
        $eligibleCount = $phase === 'result' && $published
            ? (clone $query)->whereNotNull('scholarship_percentage')->count() : count($ids);
        $token = Str::random(40);
        // Only this temporary selection is confirmed; later matching applications are excluded.
        $request->session()->put('scholarship_bulk_publication', [
            'token' => $token, 'ids' => $ids, 'phase' => $phase, 'published' => $published, 'filters' => $filters,
            'actor_id' => $request->user()->id, 'expires_at' => now()->addMinutes(30)->timestamp,
        ]);

        return view('admin.scholarship.applications.publication-preview', [
            'token' => $token, 'phase' => $phase, 'published' => $published, 'filters' => $filters,
            'count' => count($ids), 'eligibleCount' => $eligibleCount, 'scope' => $data['scope'],
            'applications' => ScholarshipApplication::query()->with('period')
                ->whereIn('id', array_slice($ids, 0, 20))->orderBy('id')->get(),
        ]);
    }

    public function publishBulk(Request $request): RedirectResponse
    {
        $data = Validator::make($request->post(), ['token' => ['required', 'string', 'size:40']], [], [
            'token' => __('scholarship.publication_preview'),
        ])->validate();
        $preview = $request->session()->get('scholarship_bulk_publication');
        if (! is_array($preview) || ! hash_equals($preview['token'], $data['token'])
            || $preview['actor_id'] !== $request->user()->id || $preview['expires_at'] < now()->timestamp) {
            throw ValidationException::withMessages(['token' => __('scholarship.publication_preview_expired')]);
        }
        $request->session()->forget('scholarship_bulk_publication');
        $updated = 0;
        $skipped = [];
        foreach (array_chunk($preview['ids'], 1000) as $ids) {
            $result = $this->applications->publish($request->user(), $ids, $preview['phase'], $preview['published']);
            $updated += count($result['updated']);
            $skipped += $result['skipped'];
        }
        $firstSkipped = array_slice($skipped, 0, 20, true);
        $numbers = ScholarshipApplication::query()->whereKey(array_keys($firstSkipped))->pluck('application_number', 'id');
        $details = [];
        foreach ($firstSkipped as $id => $errors) {
            $details[] = ['number' => $numbers[$id] ?? '#'.$id, 'reason' => collect($errors)->flatten()->implode(' ')];
        }

        return redirect()->route('admin.scholarship.applications.index', $preview['filters'])
            ->with('modalSuccessTitle', __('scholarship.publication_management'))
            ->with('modalSuccessContent', __('scholarship.publication_completed', [
                'phase' => __('scholarship.'.$preview['phase'].'_publication'),
                'state' => __('scholarship.'.($preview['published'] ? 'published' : 'unpublished')),
                'updated' => $updated, 'skipped' => count($skipped),
            ]))->with('publicationSkipped', $details);
    }

    public function updateContact(Request $request, ScholarshipApplication $application, string $phase): RedirectResponse
    {
        $data = Validator::make($request->post(), ['reached' => ['required', 'boolean']])->validate();
        $this->applications->markContact($request->user(), $application->id, $phase, (bool) $data['reached']);

        return redirect()->back()->with('modalSuccessTitle', __('scholarship.communication'))
            ->with('modalSuccessContent', __('scholarship.contact_updated'));
    }

    public function previewNotifications(Request $request): View
    {
        $data = Validator::make($request->post(), [
            'scope' => ['required', Rule::in(['selected', 'filtered'])],
            'phase' => ['required', Rule::in(['application', 'result'])],
            'channel' => ['required', Rule::in(['email', 'whatsapp'])],
            'ids' => ['required_if:scope,selected', 'array', 'max:1000'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
            'filters' => ['sometimes', 'array'],
        ], ['ids.required_if' => __('scholarship.publication_no_selection')])->validate();
        $filters = $this->filters($data['filters'] ?? []);
        $query = $this->query($filters);
        if ($data['scope'] === 'selected') {
            $query->whereIn('id', $data['ids']);
            if ((clone $query)->count() !== count($data['ids'])) {
                throw ValidationException::withMessages(['ids' => __('scholarship.selection_changed')]);
            }
        }
        $ids = $query->orderBy('id')->pluck('id')->all();
        if ($ids === []) {
            throw ValidationException::withMessages(['ids' => __('scholarship.publication_no_selection')]);
        }
        $rows = [];
        foreach ((clone $query)->with(['user', 'period', 'session.branch', 'session.examGroup'])->limit(20)->get() as $application) {
            try {
                $message = $this->notifications->message($application, $data['phase'], $data['channel']);
                $rows[] = ['application' => $application, 'message' => $message, 'error' => null];
            } catch (ValidationException $error) {
                $rows[] = ['application' => $application, 'message' => null, 'error' => collect($error->errors())->flatten()->implode(' ')];
            }
        }
        $token = Str::random(40);
        $request->session()->put('scholarship_bulk_notification', [
            'token' => $token, 'ids' => $ids, 'phase' => $data['phase'], 'channel' => $data['channel'], 'filters' => $filters,
            'actor_id' => $request->user()->id, 'expires_at' => now()->addMinutes(30)->timestamp,
        ]);

        return view('admin.scholarship.applications.notification-preview', [
            'token' => $token, 'count' => count($ids), 'rows' => $rows, 'filters' => $filters,
            'phase' => $data['phase'], 'channel' => $data['channel'], 'scope' => $data['scope'],
        ]);
    }

    public function sendNotifications(Request $request): RedirectResponse
    {
        $data = Validator::make($request->post(), ['token' => ['required', 'string', 'size:40']])->validate();
        $preview = $request->session()->get('scholarship_bulk_notification');
        if (! is_array($preview) || ! hash_equals($preview['token'], $data['token'])
            || $preview['actor_id'] !== $request->user()->id || $preview['expires_at'] < now()->timestamp) {
            throw ValidationException::withMessages(['token' => __('scholarship.approval_preview_expired')]);
        }
        $request->session()->forget('scholarship_bulk_notification');
        $counts = ['queued' => 0, 'duplicate' => 0, 'skipped' => 0];
        $details = [];
        foreach ($preview['ids'] as $id) {
            try {
                $counts[$this->notifications->enqueue($request->user(), $id, $preview['phase'], $preview['channel'])]++;
            } catch (ModelNotFoundException|ValidationException $error) {
                $counts['skipped']++;
                if (count($details) < 20) {
                    $details[] = [
                        'number' => ScholarshipApplication::query()->whereKey($id)->value('application_number') ?? '#'.$id,
                        'reason' => $error instanceof ValidationException ? collect($error->errors())->flatten()->implode(' ')
                            : __('scholarship.application_no_longer_exists'),
                    ];
                }
            }
        }

        return redirect()->route('admin.scholarship.applications.index', $preview['filters'])
            ->with('modalSuccessTitle', __('scholarship.communication'))
            ->with('modalSuccessContent', __('scholarship.delivery_summary', $counts))
            ->with('notificationSkipped', $details);
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
