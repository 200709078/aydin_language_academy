<?php

namespace App\Http\Controllers;

use App\Models\PlacementTestLevel;
use App\Models\PlacementTestQuestion;
use App\Models\PlacementTestQuestionContent;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use LogicException;

class PlacementTestQuestionController extends Controller
{
    /**
     * Display master questions with their level, shared-content group and usage.
     */
    public function index(): View
    {
        $questions = PlacementTestQuestion::query()
            ->with(['level', 'questionContent'])
            ->withCount(['options', 'levelQuestionSnapshots'])
            ->orderBy(
                PlacementTestLevel::query()
                    ->select('sequence')
                    ->whereColumn('placement_test_levels.id', 'placement_test_questions.placement_test_level_id'),
            )
            ->orderByRaw('placement_test_question_content_id IS NULL')
            ->orderBy('placement_test_question_content_id')
            ->orderBy('content_position')
            ->orderBy('id')
            ->paginate(15);

        return view('admin.placement-test.questions.index', compact('questions'));
    }

    /**
     * Show the form for a new master question and its options.
     */
    public function create(): View
    {
        $latestQuestionContent = PlacementTestQuestionContent::query()
            ->where('is_active', true)
            ->whereHas('level', fn ($query) => $query->where('has_exam', true))
            ->withMax('questions', 'content_position')
            ->latest('id')
            ->first(['id', 'placement_test_level_id']);

        $defaultContentPosition = $latestQuestionContent === null
            ? null
            : ((int) ($latestQuestionContent->questions_max_content_position ?? 0)) + 1;

        return view('admin.placement-test.questions.create', [
            'levels' => $this->examLevels(),
            'contents' => $this->questionContentsForForm(),
            'defaultLevelId' => $latestQuestionContent?->placement_test_level_id,
            'defaultContentId' => $latestQuestionContent?->id,
            'defaultContentPosition' => $defaultContentPosition,
        ]);
    }

    /**
     * Store a question and all of its display-ordered answer options.
     */
    public function store(Request $request): RedirectResponse
    {
        [$attributes, $options] = $this->validatedQuestion($request);

        try {
            DB::transaction(function () use ($attributes, $options): void {
                $question = new PlacementTestQuestion;
                $question->fill($attributes);
                $question->save();

                $question->options()->createMany($options);
            });
        } catch (QueryException) {
            return $this->databaseValidationError();
        } catch (LogicException $exception) {
            return back()->withInput()->withErrors([
                'question_text' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('placement_test_questions_list')
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => 'Soru']))
            ->with('modalSuccessContent', 'Soru eklendi.');
    }

    /**
     * Show an existing master question with its current answer options.
     */
    public function edit(PlacementTestQuestion $placementTestQuestion): View
    {
        $placementTestQuestion->load([
            'level',
            'questionContent',
            'options' => fn ($query) => $query->orderBy('display_position'),
        ]);

        return view('admin.placement-test.questions.edit', [
            'placementTestQuestion' => $placementTestQuestion,
            'levels' => $this->examLevels(),
            'contents' => $this->questionContentsForForm($placementTestQuestion),
        ]);
    }

    /**
     * Update source question data. Historical attempt snapshots remain unchanged.
     */
    public function update(Request $request, PlacementTestQuestion $placementTestQuestion): RedirectResponse
    {
        [$attributes, $options] = $this->validatedQuestion($request, $placementTestQuestion);

        try {
            DB::transaction(function () use ($placementTestQuestion, $attributes, $options): void {
                $placementTestQuestion->update($attributes);
                $placementTestQuestion->options()->delete();
                $placementTestQuestion->options()->createMany($options);
            });
        } catch (QueryException) {
            return $this->databaseValidationError();
        } catch (LogicException $exception) {
            return back()->withInput()->withErrors([
                'question_text' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('placement_test_questions_list')
            ->with('modalSuccessTitle', __('dictt.updatesuccesstitle', ['type' => 'Soru']))
            ->with('modalSuccessContent', 'Soru güncellendi. Geçmiş sınav snapshot kayıtları değiştirilmedi.');
    }

    /**
     * Delete a master question without deleting its historical attempt snapshots.
     */
    public function destroy(PlacementTestQuestion $placementTestQuestion): RedirectResponse
    {
        $historyCount = $placementTestQuestion->levelQuestionSnapshots()->count();

        try {
            DB::transaction(function () use ($placementTestQuestion): void {
                $placementTestQuestion->options()->delete();
                $placementTestQuestion->delete();
            });
        } catch (QueryException) {
            return redirect()
                ->route('placement_test_questions_list')
                ->with('error', 'Soru ilişkili kayıtlar nedeniyle silinemedi. Önce soruyu pasife almayı deneyin.');
        }

        $message = $historyCount > 0
            ? 'Soru silindi. Geçmiş sınav snapshot kayıtları korunuyor.'
            : 'Soru silindi.';

        return redirect()
            ->route('placement_test_questions_list')
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => 'Soru']))
            ->with('modalSuccessContent', $message);
    }

    /**
     * @return array{0: array<string, mixed>, 1: list<array<string, mixed>>}
     */
    private function validatedQuestion(Request $request, ?PlacementTestQuestion $question = null): array
    {
        $this->normalizeOptionalFields($request);

        $validated = $request->validate([
            'placement_test_level_id' => [
                'required',
                'integer',
                Rule::exists('placement_test_levels', 'id')->where('has_exam', true),
            ],
            'placement_test_question_content_id' => [
                'nullable',
                'integer',
                Rule::exists('placement_test_question_contents', 'id'),
            ],
            'content_position' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'question_text' => [
                'required',
                'string',
                'max:65535',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (trim((string) $value) === '') {
                        $fail('Soru metni boş olamaz.');
                    }
                },
            ],
            'points' => ['required', 'integer', 'min:1', 'max:20'],
            'is_active' => ['required', 'boolean'],
            'options' => ['required', 'array', 'min:2', 'max:65535'],
            'options.*' => ['required', 'array'],
            'options.*.text' => [
                'required',
                'string',
                'max:65535',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (trim((string) $value) === '') {
                        $fail('Şık metni boş olamaz.');
                    }
                },
            ],
            'correct_option_index' => ['required', 'integer'],
        ], [
            'placement_test_level_id.required' => 'Seviye seçimi zorunludur.',
            'placement_test_level_id.exists' => 'Yalnız A1–C1 seviyelerinden biri seçilebilir.',
            'placement_test_question_content_id.integer' => 'Geçerli bir ortak içerik seçin.',
            'placement_test_question_content_id.exists' => 'Seçilen ortak içerik bulunamadı.',
            'content_position.integer' => 'Grup içi sıra tam sayı olmalıdır.',
            'content_position.min' => 'Grup içi sıra en az 1 olmalıdır.',
            'content_position.max' => 'Grup içi sıra en fazla 65535 olabilir.',
            'question_text.required' => 'Soru metni zorunludur.',
            'question_text.max' => 'Soru metni çok uzundur.',
            'points.required' => 'Soru puanı zorunludur.',
            'points.integer' => 'Soru puanı tam sayı olmalıdır.',
            'points.min' => 'Soru puanı en az 1 olmalıdır.',
            'points.max' => 'Soru puanı en fazla 20 olabilir.',
            'is_active.required' => 'Soru durumu zorunludur.',
            'options.required' => 'En az iki şık ekleyin.',
            'options.array' => 'Şık bilgileri geçersizdir.',
            'options.min' => 'En az iki şık ekleyin.',
            'options.max' => 'Şık sayısı en fazla 65535 olabilir.',
            'options.*.text.required' => 'Şık metni zorunludur.',
            'options.*.text.max' => 'Şık metni çok uzundur.',
            'correct_option_index.required' => 'Bir doğru şık seçin.',
            'correct_option_index.integer' => 'Doğru şık seçimi geçersizdir.',
        ]);

        $contentId = $validated['placement_test_question_content_id'] ?? null;
        $contentPosition = $validated['content_position'] ?? null;

        if ($contentId === null && $contentPosition !== null) {
            throw ValidationException::withMessages([
                'content_position' => 'Bağımsız soru için grup içi sıra girilemez.',
            ]);
        }

        if ($contentId !== null && $contentPosition === null) {
            throw ValidationException::withMessages([
                'content_position' => 'Ortak içerik seçildiğinde grup içi sıra zorunludur.',
            ]);
        }

        if ($contentId !== null) {
            $content = PlacementTestQuestionContent::query()->find($contentId);

            if (
                $content === null
                || (int) $content->placement_test_level_id !== (int) $validated['placement_test_level_id']
            ) {
                throw ValidationException::withMessages([
                    'placement_test_question_content_id' => 'Ortak içerik, seçilen seviyeye ait olmalıdır.',
                ]);
            }

            if ($request->boolean('is_active') && ! $content->is_active) {
                throw ValidationException::withMessages([
                    'placement_test_question_content_id' => 'Aktif bir soru pasif ortak içeriğe bağlanamaz.',
                ]);
            }

            $duplicatePosition = PlacementTestQuestion::query()
                ->where('placement_test_question_content_id', $contentId)
                ->where('content_position', $contentPosition)
                ->when(
                    $question !== null,
                    fn ($query) => $query->where('id', '!=', $question->id),
                )
                ->exists();

            if ($duplicatePosition) {
                throw ValidationException::withMessages([
                    'content_position' => 'Bu ortak içerikte aynı grup içi sıra numarasına sahip başka bir soru var.',
                ]);
            }
        }

        $correctOptionIndex = (string) $validated['correct_option_index'];

        if (! array_key_exists($correctOptionIndex, $validated['options'])) {
            throw ValidationException::withMessages([
                'correct_option_index' => 'Doğru şık, eklenen şıklardan biri olmalıdır.',
            ]);
        }

        $options = [];

        foreach ($validated['options'] as $optionKey => $option) {
            $options[] = [
                'option_text' => trim($option['text']),
                'display_position' => count($options) + 1,
                'is_correct' => (string) $optionKey === $correctOptionIndex,
            ];
        }

        return [[
            'placement_test_level_id' => $validated['placement_test_level_id'],
            'placement_test_question_content_id' => $contentId,
            'content_position' => $contentPosition,
            'question_text' => trim($validated['question_text']),
            'points' => $validated['points'],
            'is_active' => $request->boolean('is_active'),
        ], $options];
    }

    private function normalizeOptionalFields(Request $request): void
    {
        foreach (['placement_test_question_content_id', 'content_position'] as $field) {
            if ($request->input($field) === '') {
                $request->merge([$field => null]);
            }
        }
    }

    /**
     * @return Collection<int, PlacementTestLevel>
     */
    private function examLevels(): Collection
    {
        return PlacementTestLevel::query()
            ->where('has_exam', true)
            ->orderBy('sequence')
            ->get();
    }

    /**
     * @return list<array{id: int, level_id: int, label: string, next_position: int}>
     */
    private function questionContentsForForm(?PlacementTestQuestion $currentQuestion = null): array
    {
        $currentContentId = $currentQuestion?->placement_test_question_content_id;
        $typeLabels = [
            'text' => 'Metin',
            'audio' => 'Ses',
            'image' => 'Görsel',
            'video' => 'Video',
        ];

        return PlacementTestQuestionContent::query()
            ->with('level')
            ->withMax('questions', 'content_position')
            ->whereHas('level', fn ($query) => $query->where('has_exam', true))
            ->where(function ($query) use ($currentContentId): void {
                $query->where('is_active', true);

                if ($currentContentId !== null) {
                    $query->orWhere('id', $currentContentId);
                }
            })
            ->orderBy(
                PlacementTestLevel::query()
                    ->select('sequence')
                    ->whereColumn('placement_test_levels.id', 'placement_test_question_contents.placement_test_level_id'),
            )
            ->orderBy('id')
            ->get()
            ->map(function (PlacementTestQuestionContent $content) use ($typeLabels): array {
                $summary = $content->type === 'text'
                    ? Str::limit((string) preg_replace('/\s+/', ' ', trim((string) $content->text_content)), 70)
                    : 'Sunucu dosyası';

                return [
                    'id' => $content->id,
                    'level_id' => $content->placement_test_level_id,
                    'label' => "{$content->level->code} · {$typeLabels[$content->type]} #{$content->id} — {$summary}",
                    'next_position' => ((int) ($content->questions_max_content_position ?? 0)) + 1,
                ];
            })
            ->values()
            ->all();
    }

    private function databaseValidationError(): RedirectResponse
    {
        return back()->withInput()->withErrors([
            'content_position' => 'Kayıt sırasında sıra veya ilişki çakışması oluştu. Sayfayı yenileyip tekrar deneyin.',
        ]);
    }
}
