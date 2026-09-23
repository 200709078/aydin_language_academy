<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipExamSession;
use App\Models\ScholarshipSchool;
use App\Models\ScholarshipStudentLevel;
use App\Services\ScholarshipApplicationService;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ScholarshipExamController extends Controller
{
    public function index(Request $request): View
    {
        $now = CarbonImmutable::now(config('app.timezone'));
        $futureSessions = $this->futureSessions($now);

        $periods = ScholarshipExamPeriod::query()
            ->where('is_active', true)
            ->whereHas('sessions', $futureSessions)
            ->with(['sessions' => fn ($sessions) => $futureSessions($sessions)
                ->with(['branch', 'examGroup'])
                ->withCount('applications')
                ->orderBy('exam_date')->orderBy('starts_at')->orderBy('id')])
            ->withExists(['applications as has_application' => fn ($applications) => $applications
                ->where('user_id', $request->user()->id)])
            ->orderBy('exam_starts_on')->orderBy('id')
            ->paginate(10)
            ->through(fn (ScholarshipExamPeriod $period) => $this->periodData($period, $now));

        return view('frontend.scholarship.exams.index', compact('periods'));
    }

    public function createApplication(Request $request, ScholarshipExamPeriod $period): View|RedirectResponse
    {
        $now = CarbonImmutable::now(config('app.timezone'));
        if (! $period->acceptsApplications($now)) {
            return to_route('frontend.scholarship.exams.index')->with('scholarship_notice', __('scholarship.period_not_accepting'));
        }
        if ($period->applications()->where('user_id', $request->user()->id)->exists()) {
            return to_route('frontend.scholarship.exams.index')->with('scholarship_notice', __('scholarship.member_existing_application'));
        }

        return $this->applicationForm($request, $period);
    }

    public function editApplication(Request $request, int $application, ScholarshipApplicationService $service): View|RedirectResponse
    {
        $data = $service->forMember($request->user(), $application);
        if (! $data['can_edit']) {
            return to_route('frontend.scholarship.applications.show', $application)
                ->with('scholarship_notice', __('scholarship.member_changes_unavailable'));
        }

        return $this->applicationForm($request, ScholarshipExamPeriod::query()->findOrFail($data['period']['id']), [
            'id' => $data['id'], 'student' => $data['student'], 'session_id' => $data['session']['id'],
        ]);
    }

    private function applicationForm(Request $request, ScholarshipExamPeriod $period, ?array $editing = null): View|RedirectResponse
    {
        $now = CarbonImmutable::now(config('app.timezone'));
        $futureSessions = $this->futureSessions($now);
        $period->load(['sessions' => fn ($sessions) => $futureSessions($sessions)
            ->with(['branch', 'examGroup'])->withCount('applications')
            ->orderBy('exam_date')->orderBy('starts_at')->orderBy('id')]);
        $period->setAttribute('has_application', false);
        $data = $this->periodData($period, $now, $editing['session_id'] ?? null);
        $available = collect($data['sessions'])->where('state', 'available');
        if ($available->isEmpty()) {
            if ($editing !== null) {
                return to_route('frontend.scholarship.applications.show', $editing['id'])
                    ->with('scholarship_notice', __('scholarship.member_no_available_sessions'));
            }

            return to_route('frontend.scholarship.exams.index')->with('scholarship_notice', __('scholarship.member_no_available_sessions'));
        }

        $requestedSession = $request->old('session_id', $editing['session_id'] ?? $request->query('session'));
        $requestedSessionId = (is_int($requestedSession) || is_string($requestedSession))
            && preg_match('/^[0-9]+$/D', (string) $requestedSession) ? (int) $requestedSession : null;
        $selectedSession = $available->firstWhere('id', $requestedSessionId);

        return view('frontend.scholarship.applications.create', [
            'editing' => $editing,
            'period' => $data,
            'student' => [...$request->user()->only(['name', 'email', 'phone']), 'name' => $editing['student']['name'] ?? $request->user()->name],
            'schools' => ScholarshipSchool::query()->where(fn ($query) => $query->where('is_active', true)
                ->when($editing !== null, fn ($query) => $query->orWhere('id', $editing['student']['school_id'])))
                ->orderBy('sort_order')->orderBy('name')->get(['id', 'name'])
                ->each(function ($school) use ($editing): void {
                    if ($school->id === ($editing['student']['school_id'] ?? null)) {
                        $school->name = $editing['student']['school'];
                    }
                }),
            'levels' => ScholarshipStudentLevel::query()->where(fn ($query) => $query->where('is_active', true)
                ->when($editing !== null, fn ($query) => $query->orWhere('id', $editing['student']['student_level_id'])))
                ->orderBy('sort_order')->orderBy('id')->get(['id', 'name'])
                ->each(function ($level) use ($editing): void {
                    if ($level->id === ($editing['student']['student_level_id'] ?? null)) {
                        $level->name = $editing['student']['level'];
                    }
                }),
            'selectedSession' => $selectedSession,
            'selectionUnavailable' => $requestedSession !== null && $requestedSession !== '' && $selectedSession === null,
        ]);
    }

    public function storeApplication(Request $request, ScholarshipExamPeriod $period, ScholarshipApplicationService $applications): RedirectResponse
    {
        // The account/name and period come from authentication and the route.
        // Branch/group/exam inputs only filter the form; the session is authoritative.
        $data = Arr::only($request->post(), ['session_id', 'school_id', 'student_level_id']);

        try {
            $application = $applications->create($request->user(), ['period_id' => $period->id, ...$data]);
        } catch (ValidationException $exception) {
            return to_route('frontend.scholarship.applications.create', $period)
                ->withErrors($exception->errors())
                ->withInput(array_filter($data, fn ($value) => is_string($value) || is_int($value)));
        }

        return to_route('frontend.scholarship.exams.index')->with('scholarship_application_created', [
            'user_id' => $application->user_id,
            'number' => $application->application_number,
        ]);
    }

    private function futureSessions(CarbonImmutable $now): Closure
    {
        return static fn ($query) => $query
            ->whereNull('archived_at')
            ->whereHas('branch', fn ($branch) => $branch->where('is_active', true))
            ->whereHas('examGroup', fn ($group) => $group->where('is_active', true))
            ->where(fn ($dates) => $dates
                ->where('exam_date', '>', $now->toDateString())
                ->orWhere(fn ($today) => $today
                    ->where('exam_date', $now->toDateString())
                    ->where('starts_at', '>', $now->format('H:i:s'))));
    }

    private function periodData(ScholarshipExamPeriod $period, CarbonImmutable $now, ?int $reservedSessionId = null): array
    {
        $acceptsApplications = $period->acceptsApplications($now);
        $state = match (true) {
            $acceptsApplications => 'open',
            ! $period->applications_open => 'closed',
            $period->applications_open_at?->gt($now) => 'upcoming',
            default => 'ended',
        };

        return [
            'id' => $period->id,
            'title' => $period->title,
            'description' => $period->description,
            'applications_open_at' => $period->applications_open_at?->format('d.m.Y H:i'),
            'applications_close_at' => $period->applications_close_at?->format('d.m.Y H:i'),
            'state' => $state,
            'has_application' => (bool) $period->has_application,
            'sessions' => $period->sessions->map(function (ScholarshipExamSession $session) use ($period, $acceptsApplications, $reservedSessionId) {
                $remaining = max(0, $session->capacity - $session->applications_count);

                return [
                    'id' => $session->id,
                    'branch_id' => $session->branch_id,
                    'branch' => $session->branch->name,
                    'exam_group_id' => $session->exam_group_id,
                    'exam_group' => $session->examGroup->name,
                    'exam_title' => $session->exam_title,
                    'exam_date' => $session->exam_date->format('d.m.Y'),
                    'starts_at' => substr($session->starts_at, 0, 5),
                    'ends_at' => substr($session->ends_at, 0, 5),
                    'capacity' => $session->capacity,
                    'remaining' => $remaining,
                    'state' => match (true) {
                        ! $session->is_active => 'suspended',
                        $remaining === 0 && $session->id !== $reservedSessionId => 'full',
                        ! $acceptsApplications => 'closed',
                        (bool) $period->has_application => 'applied',
                        default => 'available',
                    },
                ];
            })->all(),
        ];
    }
}
