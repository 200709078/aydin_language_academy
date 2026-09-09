<?php

namespace App\Http\Controllers;

use App\Models\ScholarshipExamPeriod;
use App\Services\ScholarshipCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminScholarshipPeriodController extends Controller
{
    public function __construct(private readonly ScholarshipCatalogService $catalog) {}

    public function index(): View
    {
        $periods = ScholarshipExamPeriod::query()
            ->withCount(['sessions', 'applications'])
            ->latest('id')
            ->paginate(20);

        return view('admin.scholarship.periods.index', compact('periods'));
    }

    public function create(): View
    {
        return view('admin.scholarship.periods.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->catalog->savePeriod($request->user(), $this->periodData($request));

        return $this->success('scholarship_period_created');
    }

    public function edit(ScholarshipExamPeriod $period): View
    {
        return view('admin.scholarship.periods.edit', compact('period'));
    }

    public function update(Request $request, ScholarshipExamPeriod $period): RedirectResponse
    {
        $this->catalog->savePeriod($request->user(), $this->periodData($request), $period->id);

        return $this->success('scholarship_period_updated');
    }

    public function updateApplications(Request $request, ScholarshipExamPeriod $period): RedirectResponse
    {
        $data = $request->validate(['applications_open' => ['required', 'boolean']]);
        $this->catalog->savePeriod($request->user(), $data, $period->id);

        return $this->success('scholarship_period_updated');
    }

    public function destroy(Request $request, ScholarshipExamPeriod $period): RedirectResponse
    {
        $this->catalog->deletePeriod($request->user(), $period->id);

        return $this->success('scholarship_period_deleted');
    }

    private function periodData(Request $request): array
    {
        // Keep form transport fields out of the shared domain contract.
        // Do not coerce booleans before the service validates their input.
        return array_replace([
            'title' => null,
            'description' => null,
            'applications_open_at' => null,
            'applications_close_at' => null,
            'exam_starts_on' => null,
            'exam_ends_on' => null,
            'is_active' => false,
            'applications_open' => false,
        ], $request->only([
            'title', 'description', 'applications_open_at', 'applications_close_at',
            'exam_starts_on', 'exam_ends_on', 'is_active', 'applications_open',
        ]));
    }

    private function success(string $message): RedirectResponse
    {
        return redirect()->route('admin.scholarship.periods.index')
            ->with('modalSuccessTitle', __('dictt.scholarship_periods'))
            ->with('modalSuccessContent', __('dictt.'.$message));
    }
}
