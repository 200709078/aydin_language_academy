<?php

namespace App\Services;

use App\Exceptions\PlacementTestConfigurationException;
use App\Exceptions\PlacementTestStateException;
use App\Models\PlacementTest;
use App\Models\PlacementTestLevel;
use App\Models\PlacementTestLevelQuestion;
use App\Models\PlacementTestLevelResult;
use App\Models\PlacementTestLevelResultContent;
use App\Models\PlacementTestQuestion;
use App\Models\PlacementTestQuestionContent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlacementTestAttemptService
{
    /**
     * @var list<string>
     */
    private const EXAM_LEVEL_CODES = ['A1', 'A2', 'B1', 'B2', 'C1'];

    private const QUESTION_CONTENT_DIRECTORY = 'placement-test/question-contents/';

    /**
     * Return the user's one open attempt, if one exists.
     */
    public function openAttemptFor(User $user): ?PlacementTest
    {
        return PlacementTest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['in_progress', 'pending_approval'])
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Start a new A1 attempt or return the existing open attempt for the user.
     */
    public function beginOrResume(User $user): PlacementTest
    {
        return DB::transaction(function () use ($user): PlacementTest {
            User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingAttempt = $this->openAttemptQuery($user)->first();

            if ($existingAttempt !== null) {
                return $existingAttempt;
            }

            $this->assertExamPathIsReady();

            $a1 = $this->levelByCode('A1');
            $attempt = new PlacementTest;
            $attempt->user_id = $user->id;
            $attempt->status = 'in_progress';
            $attempt->result_level_id = null;
            $attempt->started_at = now();
            $attempt->submitted_at = null;
            $attempt->approved_at = null;
            $attempt->approved_by = null;
            $attempt->save();

            $this->createLevelSnapshot($attempt, $a1);

            return $attempt;
        });
    }

    /**
     * Return the only incomplete level of an in-progress attempt.
     */
    public function currentLevelResultForUser(User $user, PlacementTest $attempt): PlacementTestLevelResult
    {
        $ownedAttempt = $this->ownedInProgressAttempt($user, $attempt);

        return $this->incompleteLevelResult($ownedAttempt);
    }

    /**
     * Return the first blank question, or the last one when every answer is saved.
     */
    public function resumeQuestionForUser(User $user, PlacementTest $attempt): PlacementTestLevelQuestion
    {
        $levelResult = $this->currentLevelResultForUser($user, $attempt);

        $question = $levelResult->levelQuestions()
            ->where('answer_status', 'blank')
            ->orderBy('display_position')
            ->first();

        if ($question !== null) {
            return $question;
        }

        $question = $levelResult->levelQuestions()
            ->orderByDesc('display_position')
            ->first();

        if ($question === null) {
            throw new PlacementTestStateException('Devam edilecek bir sınav sorusu bulunamadı.');
        }

        return $question;
    }

    /**
     * Ensure that a snapshot question belongs to the user's current level.
     */
    public function currentQuestionForUser(
        User $user,
        PlacementTest $attempt,
        PlacementTestLevelQuestion $question,
    ): PlacementTestLevelQuestion {
        $levelResult = $this->currentLevelResultForUser($user, $attempt);

        $currentQuestion = $levelResult->levelQuestions()
            ->with('contentSnapshot')
            ->whereKey($question->id)
            ->first();

        if ($currentQuestion === null) {
            throw new PlacementTestStateException('Bu soru aktif sınav seviyesine ait değil.');
        }

        return $currentQuestion;
    }

    /**
     * Save or clear one answer using only the immutable option snapshot.
     */
    public function saveAnswer(
        User $user,
        PlacementTest $attempt,
        PlacementTestLevelQuestion $question,
        ?int $selectedOption,
    ): PlacementTestLevelQuestion {
        return DB::transaction(function () use ($user, $attempt, $question, $selectedOption): PlacementTestLevelQuestion {
            $ownedAttempt = $this->ownedInProgressAttempt($user, $attempt, true);
            $levelResult = $this->incompleteLevelResult($ownedAttempt, true);

            $snapshotQuestion = $levelResult->levelQuestions()
                ->whereKey($question->id)
                ->lockForUpdate()
                ->first();

            if ($snapshotQuestion === null) {
                throw new PlacementTestStateException('Bu soru aktif sınav seviyesine ait değil.');
            }

            $availablePositions = collect($snapshotQuestion->options_snapshot)
                ->pluck('position')
                ->map(static fn (mixed $position): int => (int) $position)
                ->all();

            if ($selectedOption !== null && ! in_array($selectedOption, $availablePositions, true)) {
                throw new PlacementTestStateException('Seçilen şık bu soruya ait değil.');
            }

            $snapshotQuestion->selected_option = $selectedOption;
            $snapshotQuestion->answer_status = match (true) {
                $selectedOption === null => 'blank',
                $selectedOption === (int) $snapshotQuestion->correct_option_snapshot => 'correct',
                default => 'wrong',
            };
            $snapshotQuestion->answered_at = $selectedOption === null ? null : now();
            $snapshotQuestion->save();

            $this->refreshIncompleteSummary($levelResult);

            return $snapshotQuestion;
        });
    }

    /**
     * Finish the current level, advance after success or submit the full attempt.
     *
     * @return array{attempt: PlacementTest, next_level_result: ?PlacementTestLevelResult, submitted: bool}
     */
    public function completeCurrentLevel(User $user, PlacementTest $attempt): array
    {
        return DB::transaction(function () use ($user, $attempt): array {
            $ownedAttempt = $this->ownedInProgressAttempt($user, $attempt, true);
            $levelResult = $this->incompleteLevelResult($ownedAttempt, true);
            $levelResult->load('level');

            $summary = $this->summaryFor($levelResult, true);
            $passPercentageHundredths = $this->decimalToHundredths(
                $levelResult->pass_percentage_snapshot,
                'Geçme yüzdesi snapshot değeri geçersiz.',
                true,
            );
            $isSuccessful = $summary['score_percentage_hundredths'] >= $passPercentageHundredths;

            $levelResult->correct_count = $summary['correct_count'];
            $levelResult->wrong_count = $summary['wrong_count'];
            $levelResult->blank_count = $summary['blank_count'];
            $levelResult->correct_points = $this->decimalFromCents($summary['correct_points_cents']);
            $levelResult->score_percentage = $this->decimalFromHundredths($summary['score_percentage_hundredths']);
            $levelResult->result = $isSuccessful ? 'success' : 'unsuccess';
            $levelResult->completed_at = now();
            $levelResult->save();

            if (! $isSuccessful) {
                $this->submitAttempt($ownedAttempt, $levelResult->placement_test_level_id);

                return [
                    'attempt' => $ownedAttempt,
                    'next_level_result' => null,
                    'submitted' => true,
                ];
            }

            $nextLevel = $this->nextLevel($levelResult->level);

            if (! $nextLevel->is_active) {
                throw new PlacementTestConfigurationException(
                    "{$nextLevel->code} seviyesi pasif olduğu için sınava devam edilemiyor. Yönetici seviye ayarlarını kontrol etmelidir.",
                );
            }

            if ($nextLevel->has_exam) {
                $nextLevelResult = $this->createLevelSnapshot($ownedAttempt, $nextLevel);

                return [
                    'attempt' => $ownedAttempt,
                    'next_level_result' => $nextLevelResult,
                    'submitted' => false,
                ];
            }

            if ($levelResult->level->code !== 'C1' || $nextLevel->code !== 'C2') {
                throw new PlacementTestConfigurationException('Seviye geçiş sırası geçersiz.');
            }

            $this->submitAttempt($ownedAttempt, $nextLevel->id);

            return [
                'attempt' => $ownedAttempt,
                'next_level_result' => null,
                'submitted' => true,
            ];
        });
    }

    /**
     * Return the current-level content snapshot after ownership checks.
     */
    public function currentContentSnapshotForUser(
        User $user,
        PlacementTest $attempt,
        PlacementTestLevelResultContent $contentSnapshot,
    ): PlacementTestLevelResultContent {
        $levelResult = $this->currentLevelResultForUser($user, $attempt);

        $currentContentSnapshot = $levelResult->contentSnapshots()
            ->whereKey($contentSnapshot->id)
            ->first();

        if ($currentContentSnapshot === null) {
            throw new PlacementTestStateException('Bu ortak içerik aktif sınav seviyesine ait değil.');
        }

        return $currentContentSnapshot;
    }

    /**
     * @return Collection<int, PlacementTestLevelQuestion>
     */
    public function questionsForLevelResult(PlacementTestLevelResult $levelResult): Collection
    {
        return $levelResult->levelQuestions()
            ->with('contentSnapshot')
            ->orderBy('display_position')
            ->get();
    }

    private function openAttemptQuery(User $user)
    {
        return PlacementTest::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['in_progress', 'pending_approval'])
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->lockForUpdate();
    }

    private function ownedInProgressAttempt(
        User $user,
        PlacementTest $attempt,
        bool $lockForUpdate = false,
    ): PlacementTest {
        $query = PlacementTest::query()
            ->whereKey($attempt->id)
            ->where('user_id', $user->id);

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $ownedAttempt = $query->first();

        if ($ownedAttempt === null) {
            throw new PlacementTestStateException('Bu sınav kaydına erişim izniniz yok.');
        }

        if ($ownedAttempt->status !== 'in_progress') {
            throw new PlacementTestStateException('Bu sınav artık cevaplanamaz.');
        }

        return $ownedAttempt;
    }

    private function incompleteLevelResult(
        PlacementTest $attempt,
        bool $lockForUpdate = false,
    ): PlacementTestLevelResult {
        $query = $attempt->levelResults()
            ->with('level')
            ->whereNull('completed_at')
            ->orderBy(
                PlacementTestLevel::query()
                    ->select('sequence')
                    ->whereColumn('placement_test_levels.id', 'placement_test_level_results.placement_test_level_id'),
            );

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        $levelResults = $query->get();

        if ($levelResults->count() !== 1) {
            throw new PlacementTestStateException('Devam eden sınav seviyesi bulunamadı.');
        }

        $levelResult = $levelResults->first();

        if ($levelResult->result !== null) {
            throw new PlacementTestStateException('Devam eden sınav seviyesi geçersiz durumda.');
        }

        return $levelResult;
    }

    private function assertExamPathIsReady(): void
    {
        foreach (self::EXAM_LEVEL_CODES as $sequence => $code) {
            $level = $this->levelByCode($code);

            if ($level->sequence !== $sequence + 1 || ! $level->has_exam || ! $level->is_active) {
                throw new PlacementTestConfigurationException(
                    "{$code} seviyesi sınav için aktif ve doğru sırada olmalıdır.",
                );
            }

            $this->validatedMasterQuestionsForLevel($level);
        }

        $c2 = $this->levelByCode('C2');

        if ($c2->sequence !== 6 || $c2->has_exam || ! $c2->is_active) {
            throw new PlacementTestConfigurationException('C2 seviye ayarları sınav zinciriyle uyumlu değil.');
        }
    }

    private function levelByCode(string $code): PlacementTestLevel
    {
        $level = PlacementTestLevel::query()->where('code', $code)->first();

        if ($level === null) {
            throw new PlacementTestConfigurationException("{$code} seviyesi bulunamadı.");
        }

        return $level;
    }

    private function nextLevel(PlacementTestLevel $level): PlacementTestLevel
    {
        $nextLevel = PlacementTestLevel::query()
            ->where('sequence', '>', $level->sequence)
            ->orderBy('sequence')
            ->first();

        if ($nextLevel === null) {
            throw new PlacementTestConfigurationException("{$level->code} sonrasında seviye bulunamadı.");
        }

        return $nextLevel;
    }

    private function createLevelSnapshot(
        PlacementTest $attempt,
        PlacementTestLevel $level,
    ): PlacementTestLevelResult {
        if ($attempt->levelResults()->where('placement_test_level_id', $level->id)->exists()) {
            throw new PlacementTestStateException("{$level->code} seviyesi bu sınava zaten atanmış.");
        }

        $questions = $this->validatedMasterQuestionsForLevel($level);
        $totalPointsCents = $questions
            ->map(fn (PlacementTestQuestion $question): int => $this->pointsToCents($question->points))
            ->sum();

        if ($totalPointsCents <= 0) {
            throw new PlacementTestConfigurationException("{$level->code} seviyesi için toplam puan sıfırdan büyük olmalıdır.");
        }

        $passPercentageHundredths = $this->decimalToHundredths(
            $level->pass_percentage,
            "{$level->code} seviyesi için geçme yüzdesi geçersiz.",
            true,
        );

        if ($passPercentageHundredths > 10000) {
            throw new PlacementTestConfigurationException("{$level->code} seviyesi için geçme yüzdesi 100’den büyük olamaz.");
        }

        $levelResult = new PlacementTestLevelResult;
        $levelResult->placement_test_id = $attempt->id;
        $levelResult->placement_test_level_id = $level->id;
        $levelResult->question_count_snapshot = $questions->count();
        $levelResult->pass_percentage_snapshot = $this->decimalFromHundredths($passPercentageHundredths);
        $levelResult->total_points_snapshot = $this->decimalFromCents($totalPointsCents);
        $levelResult->correct_points = '0.00';
        $levelResult->correct_count = 0;
        $levelResult->wrong_count = 0;
        $levelResult->blank_count = $questions->count();
        $levelResult->score_percentage = null;
        $levelResult->result = null;
        $levelResult->started_at = now();
        $levelResult->completed_at = null;
        $levelResult->save();

        $contentSnapshotIds = $this->createContentSnapshots($levelResult, $questions);

        foreach ($questions as $displayPosition => $question) {
            $contentId = $question->placement_test_question_content_id;
            $correctOption = $question->options->firstWhere('is_correct', true);
            $contentSnapshotId = $contentId === null
                ? null
                : ($contentSnapshotIds[$contentId] ?? null);

            if ($correctOption === null) {
                throw new PlacementTestConfigurationException("{$question->id} numaralı soru için doğru şık bulunamadı.");
            }

            if ($contentId !== null && $contentSnapshotId === null) {
                throw new PlacementTestConfigurationException(
                    "{$question->id} numaralı soru için ortak içerik snapshot'ı oluşturulamadı.",
                );
            }

            $snapshotQuestion = new PlacementTestLevelQuestion;
            $snapshotQuestion->placement_test_level_result_id = $levelResult->id;
            $snapshotQuestion->placement_test_question_id = $question->id;
            $snapshotQuestion->placement_test_level_result_content_id = $contentSnapshotId;
            $snapshotQuestion->display_position = $displayPosition + 1;
            $snapshotQuestion->question_text_snapshot = $question->question_text;
            $snapshotQuestion->options_snapshot = $question->options
                ->map(static fn ($option): array => [
                    'position' => $option->display_position,
                    'text' => $option->option_text,
                ])
                ->values()
                ->all();
            $snapshotQuestion->correct_option_snapshot = $correctOption->display_position;
            $snapshotQuestion->points_snapshot = $this->decimalFromCents($this->pointsToCents($question->points));
            $snapshotQuestion->selected_option = null;
            $snapshotQuestion->answer_status = 'blank';
            $snapshotQuestion->answered_at = null;
            $snapshotQuestion->save();
        }

        return $levelResult;
    }

    /**
     * @param  Collection<int, PlacementTestQuestion>  $questions
     * @return array<int, int>
     */
    private function createContentSnapshots(
        PlacementTestLevelResult $levelResult,
        Collection $questions,
    ): array {
        $snapshotIds = [];

        foreach ($questions as $question) {
            $contentId = $question->placement_test_question_content_id;

            if ($contentId === null || array_key_exists($contentId, $snapshotIds)) {
                continue;
            }

            $content = $question->questionContent;

            if ($content === null) {
                throw new PlacementTestConfigurationException("{$question->id} numaralı soru için ortak içerik bulunamadı.");
            }

            $contentSnapshot = new PlacementTestLevelResultContent;
            $contentSnapshot->placement_test_level_result_id = $levelResult->id;
            $contentSnapshot->placement_test_level_id = $levelResult->placement_test_level_id;
            $contentSnapshot->placement_test_question_content_id = $content->id;
            $contentSnapshot->type_snapshot = $content->type;
            $contentSnapshot->text_content_snapshot = $content->text_content;
            $contentSnapshot->media_disk_snapshot = $content->media_disk;
            $contentSnapshot->media_path_snapshot = $content->media_path;
            $contentSnapshot->save();

            $snapshotIds[$contentId] = $contentSnapshot->id;
        }

        return $snapshotIds;
    }

    /**
     * @return Collection<int, PlacementTestQuestion>
     */
    private function validatedMasterQuestionsForLevel(PlacementTestLevel $level): Collection
    {
        if (! $level->has_exam || ! $level->is_active) {
            throw new PlacementTestConfigurationException("{$level->code} seviyesi aktif bir sınav seviyesi değil.");
        }

        $questions = PlacementTestQuestion::query()
            ->with([
                'options' => fn ($query) => $query->orderBy('display_position'),
                'questionContent',
            ])
            ->where('placement_test_level_id', $level->id)
            ->where('is_active', true)
            ->orderByRaw('placement_test_question_content_id IS NULL')
            ->orderBy('placement_test_question_content_id')
            ->orderBy('content_position')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            throw new PlacementTestConfigurationException("{$level->code} seviyesi için aktif soru bulunamadı.");
        }

        if ($level->question_count !== null && (int) $level->question_count !== $questions->count()) {
            throw new PlacementTestConfigurationException(
                "{$level->code} seviyesindeki aktif soru sayısı, tanımlı hedef soru sayısıyla eşleşmiyor.",
            );
        }

        $contentPositions = [];

        foreach ($questions as $question) {
            $this->validateMasterQuestion($question, $level, $contentPositions);
        }

        return $questions;
    }

    /**
     * @param  array<int, array<int, bool>>  $contentPositions
     */
    private function validateMasterQuestion(
        PlacementTestQuestion $question,
        PlacementTestLevel $level,
        array &$contentPositions,
    ): void {
        if (trim((string) $question->question_text) === '') {
            throw new PlacementTestConfigurationException("{$question->id} numaralı sorunun metni boş olamaz.");
        }

        $this->pointsToCents($question->points);

        $contentId = $question->placement_test_question_content_id;
        $contentPosition = $question->content_position;

        if (($contentId === null) !== ($contentPosition === null)) {
            throw new PlacementTestConfigurationException(
                "{$question->id} numaralı sorunun ortak içerik ve grup sırası bilgileri birlikte tanımlanmalıdır.",
            );
        }

        if ($contentId !== null) {
            if ((int) $contentPosition < 1) {
                throw new PlacementTestConfigurationException("{$question->id} numaralı sorunun grup sırası geçersiz.");
            }

            $content = $question->questionContent;

            if (
                $content === null
                || ! $content->is_active
                || (int) $content->placement_test_level_id !== (int) $level->id
            ) {
                throw new PlacementTestConfigurationException(
                    "{$question->id} numaralı sorunun ortak içeriği aktif ve aynı seviyeye ait olmalıdır.",
                );
            }

            $this->validateMasterContent($content, $question->id);

            if (isset($contentPositions[$contentId][$contentPosition])) {
                throw new PlacementTestConfigurationException(
                    "{$question->id} numaralı sorunun ortak içerik grup sırası tekrar ediyor.",
                );
            }

            $contentPositions[$contentId][$contentPosition] = true;
        }

        if ($question->options->count() < 2) {
            throw new PlacementTestConfigurationException("{$question->id} numaralı soru en az iki şık içermelidir.");
        }

        $correctOptionCount = 0;
        $positions = [];

        foreach ($question->options as $option) {
            if (trim((string) $option->option_text) === '' || (int) $option->display_position < 1) {
                throw new PlacementTestConfigurationException("{$question->id} numaralı sorunun şıkları geçersiz.");
            }

            if (isset($positions[$option->display_position])) {
                throw new PlacementTestConfigurationException("{$question->id} numaralı sorunun şık sıraları tekrar ediyor.");
            }

            $positions[$option->display_position] = true;
            $correctOptionCount += $option->is_correct ? 1 : 0;
        }

        if ($correctOptionCount !== 1) {
            throw new PlacementTestConfigurationException(
                "{$question->id} numaralı soru tam olarak bir doğru şık içermelidir.",
            );
        }
    }

    private function validateMasterContent(PlacementTestQuestionContent $content, int $questionId): void
    {
        if (! in_array($content->type, ['text', 'audio', 'image', 'video'], true)) {
            throw new PlacementTestConfigurationException("{$questionId} numaralı soru için ortak içerik türü geçersiz.");
        }

        if ($content->type === 'text') {
            if (trim((string) $content->text_content) === '') {
                throw new PlacementTestConfigurationException("{$questionId} numaralı sorunun ortak metni boş olamaz.");
            }

            return;
        }

        $mediaPath = trim((string) $content->media_path);

        if (
            $content->media_disk !== 'local'
            || $mediaPath === ''
            || ! str_starts_with($mediaPath, self::QUESTION_CONTENT_DIRECTORY)
            || ! Storage::disk('local')->exists($mediaPath)
        ) {
            throw new PlacementTestConfigurationException(
                "{$questionId} numaralı sorunun ortak medya dosyası sunucuda bulunamadı.",
            );
        }
    }

    private function refreshIncompleteSummary(PlacementTestLevelResult $levelResult): void
    {
        $summary = $this->summaryFor($levelResult, false);

        $levelResult->correct_count = $summary['correct_count'];
        $levelResult->wrong_count = $summary['wrong_count'];
        $levelResult->blank_count = $summary['blank_count'];
        $levelResult->correct_points = $this->decimalFromCents($summary['correct_points_cents']);
        $levelResult->score_percentage = null;
        $levelResult->result = null;
        $levelResult->save();
    }

    /**
     * @return array{correct_count: int, wrong_count: int, blank_count: int, correct_points_cents: int, score_percentage_hundredths: int}
     */
    private function summaryFor(PlacementTestLevelResult $levelResult, bool $lockQuestions): array
    {
        $query = $levelResult->levelQuestions()->orderBy('display_position');

        if ($lockQuestions) {
            $query->lockForUpdate();
        }

        $questions = $query->get();

        if ($questions->count() !== (int) $levelResult->question_count_snapshot) {
            throw new PlacementTestStateException('Sınav soru snapshot sayısı beklenen değerle eşleşmiyor.');
        }

        $totalPointsCents = $this->pointsToCents($levelResult->total_points_snapshot);
        $computedTotalPointsCents = 0;
        $correctPointsCents = 0;
        $correctCount = 0;
        $wrongCount = 0;
        $blankCount = 0;

        foreach ($questions as $question) {
            $pointsCents = $this->pointsToCents($question->points_snapshot);
            $computedTotalPointsCents += $pointsCents;

            $availablePositions = collect($question->options_snapshot)
                ->pluck('position')
                ->map(static fn (mixed $position): int => (int) $position)
                ->all();

            if (
                ! in_array((int) $question->correct_option_snapshot, $availablePositions, true)
                || ($question->selected_option !== null
                    && ! in_array((int) $question->selected_option, $availablePositions, true))
            ) {
                throw new PlacementTestStateException('Sınav soru seçenekleri doğrulanamadı.');
            }

            $expectedStatus = match (true) {
                $question->selected_option === null => 'blank',
                (int) $question->selected_option === (int) $question->correct_option_snapshot => 'correct',
                default => 'wrong',
            };

            if ($question->answer_status !== $expectedStatus) {
                $question->answer_status = $expectedStatus;
                $question->answered_at = $expectedStatus === 'blank' ? null : ($question->answered_at ?? now());
                $question->save();
            }

            if ($expectedStatus === 'correct') {
                $correctCount++;
                $correctPointsCents += $pointsCents;
            } elseif ($expectedStatus === 'wrong') {
                $wrongCount++;
            } else {
                $blankCount++;
            }
        }

        if ($computedTotalPointsCents !== $totalPointsCents) {
            throw new PlacementTestStateException('Sınav puan snapshot toplamı doğrulanamadı.');
        }

        return [
            'correct_count' => $correctCount,
            'wrong_count' => $wrongCount,
            'blank_count' => $blankCount,
            'correct_points_cents' => $correctPointsCents,
            'score_percentage_hundredths' => $this->scorePercentageHundredths($correctPointsCents, $totalPointsCents),
        ];
    }

    private function submitAttempt(PlacementTest $attempt, int $resultLevelId): void
    {
        $attempt->status = 'pending_approval';
        $attempt->result_level_id = $resultLevelId;
        $attempt->submitted_at = now();
        $attempt->approved_at = null;
        $attempt->approved_by = null;
        $attempt->save();
    }

    private function pointsToCents(mixed $value): int
    {
        return $this->decimalToHundredths($value, 'Soru puanı sıfırdan büyük olmalıdır.', false);
    }

    private function decimalToHundredths(mixed $value, string $message, bool $allowZero): int
    {
        $decimal = trim((string) $value);

        if (! preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $decimal, $matches)) {
            throw new PlacementTestConfigurationException($message);
        }

        $fraction = str_pad($matches[2] ?? '', 2, '0');
        $hundredths = ((int) $matches[1] * 100) + (int) $fraction;

        if ($hundredths < 0 || (! $allowZero && $hundredths === 0)) {
            throw new PlacementTestConfigurationException($message);
        }

        return $hundredths;
    }

    private function decimalFromCents(int $cents): string
    {
        return sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }

    private function decimalFromHundredths(int $hundredths): string
    {
        return sprintf('%d.%02d', intdiv($hundredths, 100), $hundredths % 100);
    }

    private function scorePercentageHundredths(int $correctPointsCents, int $totalPointsCents): int
    {
        if ($totalPointsCents <= 0) {
            throw new PlacementTestStateException('Sınav toplam puanı geçersiz.');
        }

        return intdiv(
            ($correctPointsCents * 10000) + intdiv($totalPointsCents, 2),
            $totalPointsCents,
        );
    }
}
