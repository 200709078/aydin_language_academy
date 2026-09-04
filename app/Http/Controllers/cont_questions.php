<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\QuestionOption;
use App\Models\model_exercises;
use App\Models\model_questions;
use App\Support\LegacyExerciseMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class cont_questions extends Controller
{
    public function create(int $exercises_id): View
    {
        $exercise = model_exercises::query()->findOrFail($exercises_id);

        return view('admin.questions.create', compact('exercise'));
    }

    public function store(Request $request, int $exercise_id): RedirectResponse
    {
        [$attributes, $options] = $this->validatedQuestion($request);
        $this->ensureNewOptions($options);
        $exercise = model_exercises::query()->findOrFail($exercise_id);
        $imageFileName = $this->storeImage($request);

        $question = DB::transaction(function () use ($exercise, $attributes, $options, $imageFileName): model_questions {
            $question = $exercise->questions()->create([
                ...$attributes,
                'image' => $imageFileName,
            ]);
            $question->options()->createMany($this->newOptionAttributes($options));

            return $question;
        });

        $modalSuccessTitle = __('dictt.savesuccesstitle', ['type' => __('dictt.question')]);
        $modalSuccessContent = __('dictt.savesuccesscontent', ['type' => __('dictt.question'), 'name' => $question->question]);

        return redirect()->route('questions_list', ['exercise_id' => $exercise_id])
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    public function edit(int $question_id): View
    {
        $question = model_questions::query()
            ->with('options')
            ->findOrFail($question_id);

        return view('admin.questions.edit', compact('question'));
    }

    public function update(Request $request, int $question_id): RedirectResponse
    {
        [$attributes, $options] = $this->validatedQuestion($request);
        $question = model_questions::query()->findOrFail($question_id);
        $imageFileName = $this->storeImage($request);

        DB::transaction(function () use ($question, $attributes, $options, $imageFileName): void {
            $question->update([
                ...$attributes,
                'image' => $imageFileName ?? $question->image,
            ]);
            $this->syncOptions($question, $options);
        });

        $modalSuccessTitle = __('dictt.updatesuccesstitle', ['type' => __('dictt.question')]);
        $modalSuccessContent = __('dictt.updatesuccesscontent', ['type' => __('dictt.question'), 'name' => $question->question]);

        return redirect()->route('questions_list', ['exercise_id' => $question->exercise_id])
            ->with('modalSuccessTitle', $modalSuccessTitle)
            ->with('modalSuccessContent', $modalSuccessContent);
    }

    /**
     * @return array{0: array{question: string}, 1: list<array{id: ?int, option_text: string, is_correct: bool}>}
     */
    private function validatedQuestion(Request $request): array
    {
        $validated = $request->validate([
            'question' => [
                'required',
                'string',
                'min:3',
                'max:65535',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (trim((string) $value) === '') {
                        $fail(__('dictt.required_item', ['name' => __('dictt.question')]));
                    }
                },
            ],
            'options' => ['required', 'array', 'min:2', 'max:65535'],
            'options.*' => ['required', 'array'],
            'options.*.id' => ['nullable', 'integer'],
            'options.*.text' => [
                'required',
                'string',
                'max:65535',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (trim((string) $value) === '') {
                        $fail(__('dictt.required_item', ['name' => __('dictt.answer')]));
                    }
                },
            ],
            'correct_option_index' => ['required', 'integer'],
        ], [
            'question.required' => __('dictt.required_item', ['name' => __('dictt.question')]),
            'question.min' => __('dictt.mincharacter_item', ['name' => __('dictt.question'), 'number' => 3]),
            'question.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.question'), 'number' => 65535]),
            'options.required' => __('dictt.pt_options_hint'),
            'options.array' => __('dictt.pt_options_hint'),
            'options.min' => __('dictt.pt_options_hint'),
            'options.max' => __('dictt.pt_options_hint'),
            'options.*.text.required' => __('dictt.required_item', ['name' => __('dictt.answer')]),
            'options.*.text.max' => __('dictt.maxcharacter_item', ['name' => __('dictt.answer'), 'number' => 65535]),
            'options.*.id.integer' => __('dictt.pt_options_hint'),
            'correct_option_index.required' => __('dictt.pt_options_hint'),
            'correct_option_index.integer' => __('dictt.pt_options_hint'),
        ]);

        $correctOptionIndex = (string) $validated['correct_option_index'];

        if (! array_key_exists($correctOptionIndex, $validated['options'])) {
            throw ValidationException::withMessages([
                'correct_option_index' => __('dictt.pt_options_hint'),
            ]);
        }

        $options = [];

        foreach ($validated['options'] as $optionIndex => $option) {
            $options[] = [
                'id' => isset($option['id']) ? (int) $option['id'] : null,
                'option_text' => trim($option['text']),
                'is_correct' => (string) $optionIndex === $correctOptionIndex,
            ];
        }

        return [[
            'question' => trim($validated['question']),
        ], $options];
    }

    /**
     * @param  list<array{id: ?int, option_text: string, is_correct: bool}>  $options
     */
    private function ensureNewOptions(array $options): void
    {
        if (collect($options)->contains(static fn (array $option): bool => $option['id'] !== null)) {
            throw ValidationException::withMessages([
                'options' => __('dictt.pt_options_hint'),
            ]);
        }
    }

    /**
     * @param  list<array{id: ?int, option_text: string, is_correct: bool}>  $options
     * @return list<array{option_text: string, display_position: int, is_correct: bool}>
     */
    private function newOptionAttributes(array $options): array
    {
        return collect($options)
            ->values()
            ->map(static fn (array $option, int $index): array => [
                'option_text' => $option['option_text'],
                'display_position' => $index + 1,
                'is_correct' => $option['is_correct'],
            ])
            ->all();
    }

    /**
     * Keep surviving option IDs so existing exercise answers remain meaningful.
     * Removing an option intentionally leaves any answer to it blank through the
     * nullable answer foreign key.
     *
     * @param  list<array{id: ?int, option_text: string, is_correct: bool}>  $options
     */
    private function syncOptions(model_questions $question, array $options): void
    {
        $submittedIds = collect($options)
            ->pluck('id')
            ->filter()
            ->map(static fn (int $id): int => $id)
            ->values();

        if ($submittedIds->count() !== $submittedIds->unique()->count()) {
            throw ValidationException::withMessages([
                'options' => __('dictt.pt_options_hint'),
            ]);
        }

        $existingOptions = QuestionOption::query()
            ->where('question_id', $question->id)
            ->whereIn('id', $submittedIds->all())
            ->get()
            ->keyBy('id');

        if ($existingOptions->count() !== $submittedIds->count()) {
            throw ValidationException::withMessages([
                'options' => __('dictt.pt_options_hint'),
            ]);
        }

        if ($submittedIds->isEmpty()) {
            $question->options()->delete();
        } else {
            $question->options()->whereNotIn('id', $submittedIds->all())->delete();
        }

        $nextPosition = (int) (QuestionOption::query()
            ->where('question_id', $question->id)
            ->max('display_position') ?? 0);

        foreach ($options as $option) {
            if ($option['id'] !== null) {
                $existingOptions->get($option['id'])->update([
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'],
                ]);

                continue;
            }

            $question->options()->create([
                'option_text' => $option['option_text'],
                'display_position' => ++$nextPosition,
                'is_correct' => $option['is_correct'],
            ]);
        }
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return LegacyExerciseMedia::store(
            $request->file('image'),
            LegacyExerciseMedia::QUESTION_IMAGES,
        );
    }
}
