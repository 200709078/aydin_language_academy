<?php

namespace App\Http\Controllers;

use App\Models\ScholarshipApplication;
use App\Models\ScholarshipBranch;
use App\Models\ScholarshipExamGroup;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipExamSession;
use App\Services\ScholarshipCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminScholarshipSessionController extends Controller
{
    public function __construct(private readonly ScholarshipCatalogService $catalog) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'period_id' => ['nullable', 'integer', 'exists:scholarship_exam_periods,id'],
            'branch_id' => ['nullable', 'integer', 'exists:scholarship_branches,id'],
            'exam_group_id' => ['nullable', 'integer', 'exists:scholarship_exam_groups,id'],
            'state' => ['nullable', Rule::in(['active', 'suspended', 'archived'])],
            'q' => ['nullable', 'string', 'max:150'],
        ]);
        $query = ScholarshipExamSession::query();
        foreach (['period_id', 'branch_id', 'exam_group_id'] as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (($filters['state'] ?? null) === 'archived') {
            $query->whereNotNull('archived_at');
        } elseif (in_array($filters['state'] ?? null, ['active', 'suspended'], true)) {
            $query->whereNull('archived_at')->where('is_active', $filters['state'] === 'active');
        }
        if (isset($filters['q'])) {
            $query->where('exam_title', 'like', '%'.$filters['q'].'%');
        }

        // Summaries cover the complete filter selection, independently of pagination.
        $totals = [
            'sessions' => (clone $query)->count(),
            'capacity' => (int) (clone $query)->sum('capacity'),
            'applications' => ScholarshipApplication::query()->whereIn('session_id', (clone $query)->select('id'))->count(),
        ];
        $sessions = $query->with(['period', 'branch', 'examGroup'])->withCount('applications')
            ->orderBy('exam_date')->orderBy('starts_at')->orderBy('id')
            ->paginate(20)->withQueryString();

        return view('admin.scholarship.sessions.index', [
            ...$this->choices(), ...compact('sessions', 'filters', 'totals'),
        ]);
    }

    public function create(Request $request): View
    {
        $data = $request->validate(['period_id' => ['nullable', 'integer', 'exists:scholarship_exam_periods,id']]);

        return view('admin.scholarship.sessions.create', [
            ...$this->choices(), 'selectedPeriodId' => $data['period_id'] ?? null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->catalog->saveSession($request->user(), $this->sessionData($request));

        return $this->success('session_created');
    }

    public function edit(ScholarshipExamSession $session): View
    {
        $session->loadCount('applications');

        return view('admin.scholarship.sessions.edit', [...$this->choices(), 'session' => $session]);
    }

    public function update(Request $request, ScholarshipExamSession $session): RedirectResponse
    {
        $this->catalog->saveSession($request->user(), $this->sessionData($request), $session->id);

        return $this->success('session_updated');
    }

    public function updateStatus(Request $request, ScholarshipExamSession $session): RedirectResponse
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $this->catalog->saveSession($request->user(), $data, $session->id);

        return $this->success('session_updated');
    }

    public function updateArchive(Request $request, ScholarshipExamSession $session): RedirectResponse
    {
        $data = $request->validate(['archived' => ['required', 'boolean']]);
        $this->catalog->archiveSession($request->user(), $session->id, (bool) $data['archived']);

        return $this->success('session_updated');
    }

    public function destroy(Request $request, ScholarshipExamSession $session): RedirectResponse
    {
        $this->catalog->deleteSession($request->user(), $session->id);

        return $this->success('session_deleted');
    }

    private function sessionData(Request $request): array
    {
        // A full form must validate missing required fields rather than merge old values.
        // Unchecked checkboxes arrive without a value; their explicit hidden input is optional.
        return array_replace([
            'period_id' => null,
            'branch_id' => null,
            'exam_group_id' => null,
            'exam_title' => null,
            'exam_date' => null,
            'starts_at' => null,
            'ends_at' => null,
            'capacity' => null,
            'is_active' => false,
        ], $request->only([
            'period_id', 'branch_id', 'exam_group_id', 'exam_title', 'exam_date',
            'starts_at', 'ends_at', 'capacity', 'is_active',
        ]));
    }

    private function choices(): array
    {
        return [
            'periods' => ScholarshipExamPeriod::query()->latest('id')->get(),
            'branches' => ScholarshipBranch::query()->orderBy('sort_order')->orderBy('name')->get(),
            'groups' => ScholarshipExamGroup::query()->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }

    private function success(string $message): RedirectResponse
    {
        return redirect()->route('admin.scholarship.sessions.index')
            ->with('modalSuccessTitle', __('scholarship.sessions'))
            ->with('modalSuccessContent', __('scholarship.'.$message));
    }
}
