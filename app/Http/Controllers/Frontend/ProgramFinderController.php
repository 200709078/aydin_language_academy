<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ProgramFinderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProgramFinderController extends Controller
{
    public function __construct(private readonly ProgramFinderService $programFinder)
    {
    }

    public function show(Request $request): View
    {
        return view('frontend.program-finder', $this->viewData(
            placementLevelCode: $this->placementLevelCode($request),
        ));
    }

    public function recommend(Request $request): View
    {
        $placementLevelCode = $this->placementLevelCode($request);
        $validator = Validator::make($request->all(), [
            'learner_type' => ['required', Rule::in($this->programFinder->learnerTypes())],
            'goal' => ['required', Rule::in($this->programFinder->goals())],
            'school_stage' => [
                Rule::requiredIf(fn (): bool => $request->input('learner_type') === 'student'),
                'nullable',
                Rule::in($this->programFinder->schoolStages()),
            ],
            'exam' => [
                Rule::requiredIf(fn (): bool => $request->input('goal') === 'exam'),
                'nullable',
                Rule::in($this->programFinder->exams()),
            ],
            'self_level' => [
                Rule::requiredIf($placementLevelCode === null),
                'nullable',
                Rule::in($this->programFinder->levels()),
            ],
        ], [
            'learner_type.required' => __('dictt.program_finder_validation_learner'),
            'learner_type.in' => __('dictt.program_finder_validation_learner'),
            'goal.required' => __('dictt.program_finder_validation_goal'),
            'goal.in' => __('dictt.program_finder_validation_goal'),
            'school_stage.required' => __('dictt.program_finder_validation_school_stage'),
            'school_stage.in' => __('dictt.program_finder_validation_school_stage'),
            'exam.required' => __('dictt.program_finder_validation_exam'),
            'exam.in' => __('dictt.program_finder_validation_exam'),
            'self_level.required' => __('dictt.program_finder_validation_level'),
            'self_level.in' => __('dictt.program_finder_validation_level'),
        ]);

        $validator->after(function ($validator) use ($request): void {
            $learnerType = $request->string('learner_type')->toString();
            $goal = $request->string('goal')->toString();

            if ($learnerType !== '' && $goal !== ''
                && ! in_array($goal, $this->programFinder->goalsForLearner($learnerType), true)) {
                $validator->errors()->add('goal', __('dictt.program_finder_validation_goal_for_learner'));
            }
        });

        $answers = $validator->validate();
        $recommendation = $this->programFinder->recommend($answers, $placementLevelCode);

        return view('frontend.program-finder', $this->viewData(
            placementLevelCode: $placementLevelCode,
            recommendation: $recommendation,
            answers: $answers,
        ));
    }

    /**
     * @param  array{
     *     primary: array{route: string, title_key: string, description_key: string, icon: string},
     *     alternative: array{route: string, title_key: string, description_key: string, icon: string}|null,
     *     level: array{code: string, source: 'placement_test'|'self_declaration'}
     * }|null  $recommendation
     * @param  array<string, string|null>  $answers
     * @return array<string, mixed>
     */
    private function viewData(?string $placementLevelCode, ?array $recommendation = null, array $answers = []): array
    {
        return [
            'placementLevelCode' => $placementLevelCode,
            'recommendation' => $recommendation,
            'answers' => $answers,
            'branches' => [
                ['slug' => 'ortaca', 'label_key' => 'dictt.branch_ortaca'],
                ['slug' => 'dalaman', 'label_key' => 'dictt.branch_dalaman'],
                ['slug' => 'koycegiz', 'label_key' => 'dictt.branch_koycegiz'],
            ],
        ];
    }

    private function placementLevelCode(Request $request): ?string
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return null;
        }

        $placementTest = $user->placementTests()
            ->where('status', 'approved')
            ->whereNotNull('result_level_id')
            ->with('resultLevel:id,code,sequence')
            ->orderByDesc('approved_at')
            ->orderByDesc('id')
            ->first();

        $levelCode = $placementTest?->resultLevel?->code;

        return is_string($levelCode) && in_array($levelCode, $this->programFinder->levels(), true)
            ? $levelCode
            : null;
    }
}
