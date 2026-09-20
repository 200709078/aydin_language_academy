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
use App\Services\ScholarshipRules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminScholarshipScoreController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $query = ScholarshipApplication::query()->with(['period', 'session.branch', 'session.examGroup']);
        foreach (['period_id', 'session_id', 'school_id', 'student_level_id', 'attendance_status'] as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        $query->whereHas('session', function (Builder $sessions) use ($filters): void {
            foreach (['branch_id', 'exam_group_id', 'exam_date', 'starts_at', 'ends_at'] as $field) {
                if (isset($filters[$field])) {
                    $sessions->where($field, $filters[$field]);
                }
            }
        });
        if (isset($filters['score_state'])) {
            $filters['score_state'] === 'missing' ? $query->whereNull('score') : $query->whereNotNull('score');
        }
        foreach (['score_min' => '>=', 'score_max' => '<='] as $field => $operator) {
            if (isset($filters[$field])) {
                $query->where('score', $operator, $filters[$field]);
            }
        }
        if (isset($filters['q'])) {
            $search = '%'.trim($filters['q']).'%';
            $query->where(fn (Builder $names) => $names->where('application_number', 'like', $search)
                ->orWhere('student_name_snapshot', 'like', $search)
                ->orWhereHas('user', fn (Builder $users) => $users->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)->orWhere('phone', 'like', $search)));
        }
        $sort = $filters['sort'] ?? 'score_desc';
        if ($sort !== 'name') {
            // Ungraded applications stay separate from real zero scores in either direction.
            $query->orderByRaw('score IS NULL')->orderBy('score', $sort === 'score_asc' ? 'asc' : 'desc');
        }

        return view('admin.scholarship.scores.index', [
            'filters' => $filters,
            'applications' => $query->orderBy('student_name_snapshot')->orderBy('id')->paginate(20)->withQueryString(),
            'periods' => ScholarshipExamPeriod::query()->latest('id')->get(),
            'branches' => ScholarshipBranch::query()->orderBy('sort_order')->orderBy('name')->get(),
            'groups' => ScholarshipExamGroup::query()->orderBy('sort_order')->orderBy('name')->get(),
            'schools' => ScholarshipSchool::query()->orderBy('sort_order')->orderBy('name')->get(),
            'levels' => ScholarshipStudentLevel::query()->orderBy('sort_order')->orderBy('name')->get(),
            'sessions' => ScholarshipExamSession::query()->with(['branch', 'examGroup'])->orderByDesc('exam_date')->orderBy('starts_at')->get(),
        ]);
    }

    public function update(Request $request, ScholarshipApplication $application, ScholarshipApplicationService $applications): RedirectResponse
    {
        $filters = $this->filters($request);
        // Only score/count fields in the request body reach the shared service.
        $data = Validator::make($request->post(), [
            'score' => ['bail', 'present', 'nullable', ScholarshipRules::integer(), 'integer', 'between:0,100'],
            'correct_count' => ['bail', 'sometimes', 'nullable', ScholarshipRules::integer(), 'integer', 'between:0,65535'],
            'wrong_count' => ['bail', 'sometimes', 'nullable', ScholarshipRules::integer(), 'integer', 'between:0,65535'],
            'blank_count' => ['bail', 'sometimes', 'nullable', ScholarshipRules::integer(), 'integer', 'between:0,65535'],
        ], [], [
            'score' => __('scholarship.score'), 'correct_count' => __('scholarship.correct_count'),
            'wrong_count' => __('scholarship.wrong_count'), 'blank_count' => __('scholarship.blank_count'),
        ])->validate();
        $applications->updateResult($request->user(), $application->id, $data);

        return redirect()->to(route('admin.scholarship.scores.index', $filters).'#score-row-'.$application->id)
            ->with('modalSuccessTitle', __('scholarship.score_management'))
            ->with('modalSuccessContent', __('scholarship.score_updated', ['number' => $application->application_number]));
    }

    private function filters(Request $request): array
    {
        $rules = [
            'q' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', Rule::in(['score_desc', 'score_asc', 'name'])],
            'score_state' => ['nullable', Rule::in(['entered', 'missing'])],
            'attendance_status' => ['nullable', Rule::in(['unmarked', 'attended', 'absent'])],
            'exam_date' => ['nullable', 'date_format:Y-m-d'],
            'starts_at' => ['nullable', 'date_format:H:i:s,H:i'],
            'ends_at' => ['nullable', 'date_format:H:i:s,H:i'],
        ];
        foreach (['score_min', 'score_max'] as $field) {
            $rules[$field] = ['nullable', ScholarshipRules::integer(), 'integer', 'between:0,100'];
        }
        if ($request->query('score_min') !== null && $request->query('score_min') !== '') {
            $rules['score_max'][] = 'gte:score_min';
        }
        foreach (['period_id' => 'exam_periods', 'branch_id' => 'branches', 'exam_group_id' => 'exam_groups',
            'session_id' => 'exam_sessions', 'school_id' => 'schools', 'student_level_id' => 'student_levels'] as $field => $table) {
            $rules[$field] = ['nullable', 'integer', 'exists:scholarship_'.$table.',id'];
        }

        return array_filter(Validator::make($request->query(), $rules, [], [
            'score_min' => __('scholarship.score_min'), 'score_max' => __('scholarship.score_max'),
        ])->validate(), fn ($value) => $value !== null && $value !== '');
    }
}
