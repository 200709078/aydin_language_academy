<?php

namespace Database\Seeders;

use App\Models\PlacementTest;
use App\Models\PlacementTestLevel;
use App\Models\PlacementTestLevelQuestion;
use App\Models\PlacementTestLevelResult;
use App\Models\PlacementTestLevelResultContent;
use App\Models\PlacementTestQuestion;
use App\Models\PlacementTestQuestionOption;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use LogicException;

class PlacementTestDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PlacementTestMasterDataSeeder::class);

        $levels = PlacementTestLevel::query()
            ->orderBy('sequence')
            ->get()
            ->keyBy('code');

        $admin = $this->findDemoUser('placement-demo-admin@demo.ala.test');
        $studentA2 = $this->findDemoUser('placement-demo-a2@demo.ala.test');
        $studentC2 = $this->findDemoUser('placement-demo-c2@demo.ala.test');
        $studentProgress = $this->findDemoUser('placement-demo-progress@demo.ala.test');

        $this->seedApprovedA2Attempt(
            $studentA2,
            $admin,
            $this->level($levels, 'A1'),
            $this->level($levels, 'A2'),
        );
        $this->seedPendingC2Attempt($studentC2, $levels);
        $this->seedInProgressAttempt($studentProgress, $this->level($levels, 'A1'));
    }

    private function findDemoUser(string $email): User
    {
        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            throw new LogicException("Demo user {$email} not found. Run DatabaseSeeder first.");
        }

        return $user;
    }

    private function level($levels, string $code): PlacementTestLevel
    {
        $level = $levels->get($code);

        if (! $level instanceof PlacementTestLevel) {
            throw new LogicException("Demo level {$code} not found.");
        }

        return $level;
    }

    private function seedApprovedA2Attempt(
        User $student,
        User $admin,
        PlacementTestLevel $a1,
        PlacementTestLevel $a2,
    ): void {
        $startedAt = CarbonImmutable::parse('2026-01-10 09:00:00');
        $test = $this->findOrCreateTest($student, [
            'status' => 'approved',
            'result_level_id' => $a2->id,
            'started_at' => $startedAt,
            'submitted_at' => CarbonImmutable::parse('2026-01-10 09:28:00'),
            'approved_at' => CarbonImmutable::parse('2026-01-10 10:00:00'),
            'approved_by' => $admin->id,
        ]);

        $this->seedLevelResult(
            $test,
            $a1,
            $this->answerStatusesForOutcome($a1, true),
            $startedAt,
            CarbonImmutable::parse('2026-01-10 09:12:00'),
        );
        $this->seedLevelResult(
            $test,
            $a2,
            $this->answerStatusesForOutcome($a2, false),
            CarbonImmutable::parse('2026-01-10 09:13:00'),
            CarbonImmutable::parse('2026-01-10 09:28:00'),
        );
    }

    private function seedPendingC2Attempt(User $student, $levels): void
    {
        $startedAt = CarbonImmutable::parse('2026-02-14 10:00:00');
        $test = $this->findOrCreateTest($student, [
            'status' => 'pending_approval',
            'result_level_id' => $this->level($levels, 'C2')->id,
            'started_at' => $startedAt,
            'submitted_at' => CarbonImmutable::parse('2026-02-14 11:20:00'),
            'approved_at' => null,
            'approved_by' => null,
        ]);

        foreach (['A1', 'A2', 'B1', 'B2', 'C1'] as $offset => $levelCode) {
            $level = $this->level($levels, $levelCode);
            $levelStartedAt = $startedAt->addMinutes($offset * 15);

            $this->seedLevelResult(
                $test,
                $level,
                $this->answerStatusesForOutcome($level, true),
                $levelStartedAt,
                $levelStartedAt->addMinutes(14),
            );
        }
    }

    private function seedInProgressAttempt(User $student, PlacementTestLevel $a1): void
    {
        $startedAt = CarbonImmutable::parse('2026-03-03 14:00:00');
        $test = $this->findOrCreateTest($student, [
            'status' => 'in_progress',
            'result_level_id' => null,
            'started_at' => $startedAt,
            'submitted_at' => null,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        $this->seedLevelResult(
            $test,
            $a1,
            $this->answerStatusesForInProgress($a1),
            $startedAt,
            null,
        );
    }

    /**
     * @return list<string>
     */
    private function answerStatusesForOutcome(PlacementTestLevel $level, bool $shouldPass): array
    {
        $questions = $this->activeQuestionsForLevel($level);

        if ($questions === []) {
            throw new LogicException("{$level->code} seviyesi için aktif demo sorusu bulunamadı.");
        }

        $passPercentage = (float) $level->pass_percentage;

        if ($passPercentage <= 0) {
            throw new LogicException("{$level->code} seviyesi için geçerli bir geçme yüzdesi bulunamadı.");
        }

        $points = array_map(
            fn (PlacementTestQuestion $question): int => $this->pointsToCents($question->points),
            $questions,
        );
        $requiredCorrectPoints = (int) ceil((array_sum($points) * $passPercentage) / 100);
        $statuses = array_fill(0, count($questions), 'blank');
        $correctPoints = 0;

        foreach ($points as $index => $questionPoints) {
            if (
                ($shouldPass && $correctPoints < $requiredCorrectPoints)
                || (! $shouldPass && ($correctPoints + $questionPoints) < $requiredCorrectPoints)
            ) {
                $statuses[$index] = 'correct';
                $correctPoints += $questionPoints;
            }
        }

        if (! $shouldPass) {
            $wrongIndex = array_search('blank', $statuses, true);

            if ($wrongIndex !== false) {
                $statuses[$wrongIndex] = 'wrong';
            }
        }

        return $statuses;
    }

    /**
     * @return list<string>
     */
    private function answerStatusesForInProgress(PlacementTestLevel $level): array
    {
        $questions = $this->activeQuestionsForLevel($level);

        if ($questions === []) {
            throw new LogicException("{$level->code} seviyesi için aktif demo sorusu bulunamadı.");
        }

        $statuses = array_fill(0, count($questions), 'blank');
        $statuses[0] = 'correct';

        if (count($statuses) > 1) {
            $statuses[1] = 'wrong';
        }

        return $statuses;
    }

    /**
     * @param  array{status: string, result_level_id: ?int, started_at: CarbonImmutable, submitted_at: ?CarbonImmutable, approved_at: ?CarbonImmutable, approved_by: ?int}  $attributes
     */
    private function findOrCreateTest(User $student, array $attributes): PlacementTest
    {
        $test = PlacementTest::query()
            ->where('user_id', $student->id)
            ->where('started_at', $attributes['started_at'])
            ->first();

        if ($test !== null) {
            return $test;
        }

        $test = new PlacementTest;
        $test->user_id = $student->id;
        $test->status = $attributes['status'];
        $test->result_level_id = $attributes['result_level_id'];
        $test->started_at = $attributes['started_at'];
        $test->submitted_at = $attributes['submitted_at'];
        $test->approved_at = $attributes['approved_at'];
        $test->approved_by = $attributes['approved_by'];
        $test->save();

        return $test;
    }

    /**
     * @param  list<string>  $answerStatuses
     */
    private function seedLevelResult(
        PlacementTest $test,
        PlacementTestLevel $level,
        array $answerStatuses,
        CarbonImmutable $startedAt,
        ?CarbonImmutable $completedAt,
    ): void {
        $questions = $this->activeQuestionsForLevel($level);

        if ($questions === []) {
            throw new LogicException("{$level->code} seviyesi için aktif demo sorusu bulunamadı.");
        }

        $resolvedStatuses = [];
        $totalPointsCents = 0;
        $correctPointsCents = 0;

        foreach ($questions as $index => $question) {
            $status = $answerStatuses[$index] ?? 'blank';

            if (! in_array($status, ['correct', 'wrong', 'blank'], true)) {
                throw new LogicException("Geçersiz cevap durumu: {$status}");
            }

            $pointsCents = $this->pointsToCents($question->points);
            $totalPointsCents += $pointsCents;
            $correctPointsCents += $status === 'correct' ? $pointsCents : 0;
            $resolvedStatuses[] = $status;
        }

        $counts = array_count_values($resolvedStatuses);
        $isCompleted = $completedAt !== null;
        $scorePercentage = $isCompleted
            ? round(($correctPointsCents / $totalPointsCents) * 100, 2)
            : null;
        $passPercentage = $level->pass_percentage;

        if ($passPercentage === null) {
            throw new LogicException("{$level->code} seviyesi için geçme yüzdesi tanımlı değil.");
        }

        $levelResult = PlacementTestLevelResult::query()
            ->where('placement_test_id', $test->id)
            ->where('placement_test_level_id', $level->id)
            ->first();

        if ($levelResult === null) {
            $levelResult = new PlacementTestLevelResult;
            $levelResult->placement_test_id = $test->id;
            $levelResult->placement_test_level_id = $level->id;
            $levelResult->question_count_snapshot = count($questions);
            $levelResult->pass_percentage_snapshot = $passPercentage;
            $levelResult->total_points_snapshot = $this->centsToDecimal($totalPointsCents);
            $levelResult->correct_points = $this->centsToDecimal($correctPointsCents);
            $levelResult->correct_count = $counts['correct'] ?? 0;
            $levelResult->wrong_count = $counts['wrong'] ?? 0;
            $levelResult->blank_count = $counts['blank'] ?? 0;
            $levelResult->score_percentage = $scorePercentage;
            $levelResult->result = $isCompleted
                ? ($scorePercentage >= (float) $passPercentage ? 'success' : 'unsuccess')
                : null;
            $levelResult->started_at = $startedAt;
            $levelResult->completed_at = $completedAt;
            $levelResult->save();
        }

        $contentSnapshotIds = $this->seedContentSnapshots($levelResult, $questions);

        foreach ($questions as $index => $question) {
            $contentId = $question->placement_test_question_content_id;

            $this->seedLevelQuestionSnapshot(
                $levelResult,
                $question,
                $contentId === null ? null : ($contentSnapshotIds[$contentId] ?? null),
                $index + 1,
                $resolvedStatuses[$index],
                $startedAt,
            );
        }
    }

    /**
     * @return list<PlacementTestQuestion>
     */
    private function activeQuestionsForLevel(PlacementTestLevel $level): array
    {
        return PlacementTestQuestion::query()
            ->with([
                'options' => fn ($query) => $query->orderBy('display_position'),
                'questionContent',
            ])
            ->where('placement_test_level_id', $level->id)
            ->where('is_active', true)
            ->orderByRaw('placement_test_question_content_id is null')
            ->orderBy('placement_test_question_content_id')
            ->orderBy('content_position')
            ->orderBy('id')
            ->get()
            ->all();
    }

    /**
     * @param  list<PlacementTestQuestion>  $questions
     * @return array<int, int>
     */
    private function seedContentSnapshots(PlacementTestLevelResult $levelResult, array $questions): array
    {
        $snapshotIds = [];

        foreach ($questions as $question) {
            $contentId = $question->placement_test_question_content_id;

            if ($contentId === null || array_key_exists($contentId, $snapshotIds)) {
                continue;
            }

            $content = $question->questionContent;

            if ($content === null) {
                throw new LogicException("{$question->id} numaralı soru için ortak içerik bulunamadı.");
            }

            $snapshot = PlacementTestLevelResultContent::query()
                ->where('placement_test_level_result_id', $levelResult->id)
                ->where('placement_test_question_content_id', $content->id)
                ->first();

            if ($snapshot === null) {
                $snapshot = new PlacementTestLevelResultContent;
                $snapshot->placement_test_level_result_id = $levelResult->id;
                $snapshot->placement_test_level_id = $levelResult->placement_test_level_id;
                $snapshot->placement_test_question_content_id = $content->id;
                $snapshot->type_snapshot = $content->type;
                $snapshot->text_content_snapshot = $content->text_content;
                $snapshot->media_disk_snapshot = $content->media_disk;
                $snapshot->media_path_snapshot = $content->media_path;
                $snapshot->save();
            }

            $snapshotIds[$contentId] = $snapshot->id;
        }

        return $snapshotIds;
    }

    private function seedLevelQuestionSnapshot(
        PlacementTestLevelResult $levelResult,
        PlacementTestQuestion $question,
        ?int $contentSnapshotId,
        int $displayPosition,
        string $answerStatus,
        CarbonImmutable $startedAt,
    ): void {
        $snapshot = PlacementTestLevelQuestion::query()
            ->where('placement_test_level_result_id', $levelResult->id)
            ->where('display_position', $displayPosition)
            ->first();

        if ($snapshot !== null) {
            return;
        }

        $options = $question->options;
        $correctOption = $options->firstWhere('is_correct', true);
        $wrongOption = $options->first(
            static fn (PlacementTestQuestionOption $option): bool => $option->display_position !== $correctOption?->display_position,
        );

        if ($correctOption === null || $wrongOption === null) {
            throw new LogicException("{$question->id} numaralı demo sorununun seçenekleri geçersiz.");
        }

        $snapshot = new PlacementTestLevelQuestion;
        $snapshot->placement_test_level_result_id = $levelResult->id;
        $snapshot->placement_test_question_id = $question->id;
        $snapshot->placement_test_level_result_content_id = $contentSnapshotId;
        $snapshot->display_position = $displayPosition;
        $snapshot->question_text_snapshot = $question->question_text;
        $snapshot->options_snapshot = $options
            ->map(static fn (PlacementTestQuestionOption $option): array => [
                'position' => $option->display_position,
                'text' => $option->option_text,
            ])
            ->values()
            ->all();
        $snapshot->correct_option_snapshot = $correctOption->display_position;
        $snapshot->points_snapshot = $question->points;
        $snapshot->selected_option = match ($answerStatus) {
            'correct' => $correctOption->display_position,
            'wrong' => $wrongOption->display_position,
            default => null,
        };
        $snapshot->answer_status = $answerStatus;
        $snapshot->answered_at = $answerStatus === 'blank'
            ? null
            : $startedAt->addMinutes($displayPosition);
        $snapshot->save();
    }

    private function pointsToCents(?string $points): int
    {
        if ($points === null || ! preg_match('/^(\d+)\.(\d{2})$/', $points, $matches)) {
            throw new LogicException('Her aktif demo sorusu iki ondalıklı, pozitif bir puana sahip olmalıdır.');
        }

        $cents = ((int) $matches[1] * 100) + (int) $matches[2];

        if ($cents <= 0) {
            throw new LogicException('Her aktif demo sorusunun puanı sıfırdan büyük olmalıdır.');
        }

        return $cents;
    }

    private function centsToDecimal(int $cents): string
    {
        return sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }
}
