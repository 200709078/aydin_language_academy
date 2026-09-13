<?php

namespace App\Http\Controllers;

use App\Models\ScholarshipApplication;
use App\Models\ScholarshipBranch;
use App\Models\ScholarshipExamGroup;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipStudentLevel;
use App\Services\ScholarshipApplicationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminScholarshipAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $query = ScholarshipApplication::query()->with(['period', 'session.branch', 'session.examGroup']);

        foreach (['period_id', 'student_level_id', 'attendance_status'] as $field) {
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
        if (isset($filters['q'])) {
            $search = '%'.trim($filters['q']).'%';
            $query->where(fn (Builder $names) => $names->where('application_number', 'like', $search)
                ->orWhere('student_name_snapshot', 'like', $search)
                ->orWhereHas('user', fn (Builder $users) => $users->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)->orWhere('phone', 'like', $search)));
        }

        return view('admin.scholarship.attendance.index', [
            'filters' => $filters,
            'applications' => $query->orderBy('student_name_snapshot')->orderBy('id')->paginate(20)->withQueryString(),
            'periods' => ScholarshipExamPeriod::query()->latest('id')->get(),
            'branches' => ScholarshipBranch::query()->orderBy('sort_order')->orderBy('name')->get(),
            'groups' => ScholarshipExamGroup::query()->orderBy('sort_order')->orderBy('name')->get(),
            'levels' => ScholarshipStudentLevel::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ScholarshipApplication $application, ScholarshipApplicationService $applications): RedirectResponse
    {
        $filters = $this->filters($request);
        // Query filters and forged result/owner/publication fields must never become update data.
        $data = Validator::make($request->post(), [
            'attendance_status' => ['required', Rule::in(['unmarked', 'attended', 'absent'])],
        ], [], ['attendance_status' => __('scholarship.attendance')])->validate();

        $applications->updateResult($request->user(), $application->id, $data);

        return redirect()->to(route('admin.scholarship.attendance.index', $filters).'#attendance-row-'.$application->id)
            ->with('modalSuccessTitle', __('scholarship.attendance_management'))
            ->with('modalSuccessContent', __('scholarship.attendance_updated', ['number' => $application->application_number]));
    }

    private function filters(Request $request): array
    {
        $rules = [
            'q' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'attendance_status' => ['nullable', Rule::in(['unmarked', 'attended', 'absent'])],
            'exam_date' => ['nullable', 'date_format:Y-m-d'],
            'starts_at' => ['nullable', 'date_format:H:i:s,H:i'],
            'ends_at' => ['nullable', 'date_format:H:i:s,H:i'],
        ];
        foreach (['period_id' => 'exam_periods', 'branch_id' => 'branches',
            'exam_group_id' => 'exam_groups', 'student_level_id' => 'student_levels'] as $field => $table) {
            $rules[$field] = ['nullable', 'integer', 'exists:scholarship_'.$table.',id'];
        }

        return array_filter(Validator::make($request->query(), $rules)->validate(), fn ($value) => $value !== null && $value !== '');
    }
}
