<?php

namespace App\Http\Controllers;

use App\Models\ScholarshipBranch;
use App\Models\ScholarshipExamGroup;
use App\Models\ScholarshipSchool;
use App\Models\ScholarshipStudentLevel;
use App\Services\ScholarshipCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminScholarshipDefinitionController extends Controller
{
    public function __construct(private readonly ScholarshipCatalogService $catalog) {}

    public function index(Request $request, string $type): View
    {
        $meta = $this->metadata($type);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['all', 'active', 'inactive'])],
        ]);
        $search = trim($filters['q'] ?? '');
        $status = $filters['status'] ?? 'all';
        $definitions = $meta['model']::query()
            ->withCount([$meta['relation'].' as references_count'])
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->when($status !== 'all', fn ($query) => $query->where('is_active', $status === 'active'))
            ->orderBy('sort_order')->orderBy('name')->orderBy('id')
            ->paginate(20)->withQueryString();

        return view('admin.scholarship.definitions.index', compact('type', 'meta', 'definitions', 'search', 'status'));
    }

    public function create(string $type): View
    {
        $meta = $this->metadata($type);

        return view('admin.scholarship.definitions.create', compact('type', 'meta'));
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $this->metadata($type);
        $this->catalog->saveDefinition($request->user(), $type, $this->definitionData($request, $type));

        return $this->success($type, 'definition_created');
    }

    public function edit(string $type, int $definition): View
    {
        $meta = $this->metadata($type);
        $definition = $meta['model']::query()->findOrFail($definition);

        return view('admin.scholarship.definitions.edit', compact('type', 'meta', 'definition'));
    }

    public function update(Request $request, string $type, int $definition): RedirectResponse
    {
        $this->metadata($type);
        $this->catalog->saveDefinition($request->user(), $type, $this->definitionData($request, $type), $definition);

        return $this->success($type, 'definition_updated');
    }

    public function updateStatus(Request $request, string $type, int $definition): RedirectResponse
    {
        $this->metadata($type);
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $this->catalog->saveDefinition($request->user(), $type, $data, $definition);

        return $this->success($type, 'definition_updated');
    }

    public function destroy(Request $request, string $type, int $definition): RedirectResponse
    {
        $this->metadata($type);
        $this->catalog->deleteDefinition($request->user(), $type, $definition);

        return $this->success($type, 'definition_deleted');
    }

    private function metadata(string $type): array
    {
        return match ($type) {
            'branch' => ['model' => ScholarshipBranch::class, 'title' => 'branches', 'name' => 'branch_name', 'help' => 'branches_help', 'relation' => 'sessions', 'max_name' => 150],
            'school' => ['model' => ScholarshipSchool::class, 'title' => 'schools', 'name' => 'school_name', 'help' => 'schools_help', 'relation' => 'applications', 'max_name' => 255],
            'student_level' => ['model' => ScholarshipStudentLevel::class, 'title' => 'student_levels', 'name' => 'student_level_name', 'help' => 'student_levels_help', 'relation' => 'applications', 'max_name' => 100],
            'exam_group' => ['model' => ScholarshipExamGroup::class, 'title' => 'exam_groups', 'name' => 'exam_group_name', 'help' => 'exam_groups_help', 'relation' => 'sessions', 'max_name' => 100],
            default => abort(404),
        };
    }

    private function definitionData(Request $request, string $type): array
    {
        $defaults = ['name' => null, 'sort_order' => 0, 'is_active' => false];
        if ($type !== 'school') {
            $defaults['code'] = null;
        }
        if ($type === 'branch') {
            $defaults['address'] = null;
        }

        return array_replace($defaults, $request->only(array_keys($defaults)));
    }

    private function success(string $type, string $message): RedirectResponse
    {
        return redirect()->route('admin.scholarship.definitions.index', ['type' => $type])
            ->with('modalSuccessTitle', __('scholarship.'.$this->metadata($type)['title']))
            ->with('modalSuccessContent', __('scholarship.'.$message));
    }
}
