<?php

namespace Database\Seeders;

use App\Models\PlacementTest;
use App\Models\PlacementTestLevel;
use App\Models\PlacementTestLevelQuestion;
use App\Models\PlacementTestLevelResult;
use App\Models\PlacementTestQuestion;
use App\Models\PlacementTestQuestionOption;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlacementTestDemoSeeder extends Seeder
{
    /**
     * Seed a deterministic demo dataset for the new placement-test domain.
     */
    public function run(): void
    {
        $this->call(PlacementTestLevelSeeder::class);

        $levels = PlacementTestLevel::query()->get()->keyBy('code');
        $admin = $this->findOrCreateDemoUser([
            'name' => 'Placement Demo Admin',
            'email' => 'placement-demo-admin@demo.ala.test',
            'phone' => '+900000000001',
            'type' => 'admin',
        ]);
        $studentA2 = $this->findOrCreateDemoUser([
            'name' => 'Placement Demo Öğrenci A2',
            'email' => 'placement-demo-a2@demo.ala.test',
            'phone' => '+900000000002',
            'type' => 'user',
        ]);
        $studentC2 = $this->findOrCreateDemoUser([
            'name' => 'Placement Demo Öğrenci C2',
            'email' => 'placement-demo-c2@demo.ala.test',
            'phone' => '+900000000003',
            'type' => 'user',
        ]);
        $studentProgress = $this->findOrCreateDemoUser([
            'name' => 'Placement Demo Öğrenci Devam Eden',
            'email' => 'placement-demo-progress@demo.ala.test',
            'phone' => '+900000000004',
            'type' => 'user',
        ]);

        $questionsByLevel = $this->seedQuestionBank($levels);

        $this->seedApprovedA2Attempt(
            $studentA2,
            $admin,
            $levels,
            $questionsByLevel,
        );
        $this->seedPendingC2Attempt(
            $studentC2,
            $levels,
            $questionsByLevel,
        );
        $this->seedInProgressAttempt(
            $studentProgress,
            $levels,
            $questionsByLevel,
        );
    }

    /**
     * @param array{name: string, email: string, phone: string, type: string} $attributes
     */
    private function findOrCreateDemoUser(array $attributes): User
    {
        $user = User::query()->where('email', $attributes['email'])->first();

        if ($user !== null) {
            return $user;
        }

        $user = new User;
        $user->name = $attributes['name'];
        $user->email = $attributes['email'];
        $user->phone = $attributes['phone'];
        $user->email_verified_at = now();
        $user->password = Hash::make('placement-demo-password');
        $user->type = $attributes['type'];
        $user->save();

        return $user;
    }

    /**
     * @param \Illuminate\Support\Collection<string, PlacementTestLevel> $levels
     * @return array<string, list<PlacementTestQuestion>>
     */
    private function seedQuestionBank($levels): array
    {
        $questionsByLevel = [];

        foreach ($this->questionBank() as $levelCode => $questions) {
            $level = $levels->get($levelCode);

            foreach ($questions as $questionData) {
                $question = PlacementTestQuestion::query()->firstOrCreate(
                    [
                        'placement_test_level_id' => $level->id,
                        'question_text' => $questionData['question_text'],
                    ],
                    ['is_active' => true],
                );

                foreach ($questionData['options'] as $position => $option) {
                    $displayPosition = $position + 1;

                    PlacementTestQuestionOption::query()->firstOrCreate(
                        [
                            'placement_test_question_id' => $question->id,
                            'display_position' => $displayPosition,
                        ],
                        [
                            'option_text' => $option['text'],
                            'is_correct' => $option['is_correct'],
                        ],
                    );
                }

                $questionsByLevel[$levelCode][] = $question;
            }
        }

        return $questionsByLevel;
    }

    /**
     * @param \Illuminate\Support\Collection<string, PlacementTestLevel> $levels
     * @param array<string, list<PlacementTestQuestion>> $questionsByLevel
     */
    private function seedApprovedA2Attempt(
        User $student,
        User $admin,
        $levels,
        array $questionsByLevel,
    ): void {
        $startedAt = CarbonImmutable::parse('2026-01-10 09:00:00');
        $test = $this->findOrCreateTest($student, [
            'status' => 'approved',
            'result_level_id' => $levels->get('A2')->id,
            'started_at' => $startedAt,
            'submitted_at' => CarbonImmutable::parse('2026-01-10 09:28:00'),
            'approved_at' => CarbonImmutable::parse('2026-01-10 10:00:00'),
            'approved_by' => $admin->id,
        ]);

        $this->seedLevelResult(
            $test,
            $levels->get('A1'),
            $this->orderedQuestions($questionsByLevel['A1'], [3, 1, 5, 2, 4]),
            ['correct', 'correct', 'correct', 'wrong', 'blank'],
            $startedAt,
            CarbonImmutable::parse('2026-01-10 09:12:00'),
        );
        $this->seedLevelResult(
            $test,
            $levels->get('A2'),
            $this->orderedQuestions($questionsByLevel['A2'], [2, 5, 1, 4, 3]),
            ['correct', 'correct', 'wrong', 'wrong', 'blank'],
            CarbonImmutable::parse('2026-01-10 09:13:00'),
            CarbonImmutable::parse('2026-01-10 09:28:00'),
        );
    }

    /**
     * @param \Illuminate\Support\Collection<string, PlacementTestLevel> $levels
     * @param array<string, list<PlacementTestQuestion>> $questionsByLevel
     */
    private function seedPendingC2Attempt(
        User $student,
        $levels,
        array $questionsByLevel,
    ): void {
        $startedAt = CarbonImmutable::parse('2026-02-14 10:00:00');
        $test = $this->findOrCreateTest($student, [
            'status' => 'pending_approval',
            'result_level_id' => $levels->get('C2')->id,
            'started_at' => $startedAt,
            'submitted_at' => CarbonImmutable::parse('2026-02-14 11:20:00'),
            'approved_at' => null,
            'approved_by' => null,
        ]);

        foreach (['A1', 'A2', 'B1', 'B2', 'C1'] as $offset => $levelCode) {
            $levelStartedAt = $startedAt->addMinutes($offset * 15);

            $this->seedLevelResult(
                $test,
                $levels->get($levelCode),
                $this->orderedQuestions($questionsByLevel[$levelCode], [4, 2, 5, 1, 3]),
                ['correct', 'correct', 'correct', 'wrong', 'blank'],
                $levelStartedAt,
                $levelStartedAt->addMinutes(14),
            );
        }
    }

    /**
     * @param \Illuminate\Support\Collection<string, PlacementTestLevel> $levels
     * @param array<string, list<PlacementTestQuestion>> $questionsByLevel
     */
    private function seedInProgressAttempt(
        User $student,
        $levels,
        array $questionsByLevel,
    ): void {
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
            $levels->get('A1'),
            $this->orderedQuestions($questionsByLevel['A1'], [5, 2, 4, 1, 3]),
            ['correct', 'wrong', 'blank', 'blank', 'blank'],
            $startedAt,
            null,
        );
    }

    /**
     * @param array{status: string, result_level_id: ?int, started_at: CarbonImmutable, submitted_at: ?CarbonImmutable, approved_at: ?CarbonImmutable, approved_by: ?int} $attributes
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
     * @param list<PlacementTestQuestion> $questions
     * @param list<int> $order
     * @return list<PlacementTestQuestion>
     */
    private function orderedQuestions(array $questions, array $order): array
    {
        return array_map(
            static fn (int $position): PlacementTestQuestion => $questions[$position - 1],
            $order,
        );
    }

    /**
     * @param list<PlacementTestQuestion> $questions
     * @param list<string> $answerStatuses
     */
    private function seedLevelResult(
        PlacementTest $test,
        PlacementTestLevel $level,
        array $questions,
        array $answerStatuses,
        CarbonImmutable $startedAt,
        ?CarbonImmutable $completedAt,
    ): void {
        $counts = array_count_values($answerStatuses);
        $questionCount = count($questions);
        $isCompleted = $completedAt !== null;

        $levelResult = PlacementTestLevelResult::query()
            ->where('placement_test_id', $test->id)
            ->where('placement_test_level_id', $level->id)
            ->first();

        if ($levelResult === null) {
            $levelResult = new PlacementTestLevelResult;
            $levelResult->placement_test_id = $test->id;
            $levelResult->placement_test_level_id = $level->id;
            $levelResult->question_count_snapshot = $questionCount;
            $levelResult->pass_percentage_snapshot = 60;
            $levelResult->correct_count = $counts['correct'] ?? 0;
            $levelResult->wrong_count = $counts['wrong'] ?? 0;
            $levelResult->blank_count = $counts['blank'] ?? 0;
            $levelResult->score_percentage = $isCompleted
                ? round((($counts['correct'] ?? 0) / $questionCount) * 100, 2)
                : null;
            $levelResult->result = $isCompleted
                ? (($levelResult->score_percentage >= 60) ? 'success' : 'unsuccess')
                : null;
            $levelResult->started_at = $startedAt;
            $levelResult->completed_at = $completedAt;
            $levelResult->save();
        }

        foreach ($questions as $index => $question) {
            $this->seedLevelQuestionSnapshot(
                $levelResult,
                $question,
                $index + 1,
                $answerStatuses[$index],
                $startedAt,
            );
        }
    }

    private function seedLevelQuestionSnapshot(
        PlacementTestLevelResult $levelResult,
        PlacementTestQuestion $question,
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

        $options = $question->options()->orderBy('display_position')->get();
        $correctOption = $options->firstWhere('is_correct', true);
        $wrongOption = $options->first(
            static fn (PlacementTestQuestionOption $option): bool => $option->display_position !== $correctOption->display_position,
        );

        $snapshot = new PlacementTestLevelQuestion;
        $snapshot->placement_test_level_result_id = $levelResult->id;
        $snapshot->placement_test_question_id = $question->id;
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

    /**
     * @return array<string, list<array{question_text: string, options: list<array{text: string, is_correct: bool}>}>>
     */
    private function questionBank(): array
    {
        return [
            'A1' => [
                $this->question('I ___ a student.', ['am', 'is', 'are', 'be'], 1),
                $this->question('Choose the correct article: ___ apple.', ['A', 'An', 'The', 'No article'], 2),
                $this->question("What is the opposite of 'big'?", ['Small', 'Long', 'Fast', 'Old'], 1),
                $this->question('Ayşe and I are friends. ___ are happy.', ['We', 'She', 'He', 'It'], 1),
                $this->question('There ___ three books on the table.', ['are', 'is', 'am', 'be'], 1),
            ],
            'A2' => [
                $this->question('Yesterday, we ___ to the park.', ['go', 'went', 'gone', 'going'], 2),
                $this->question('My brother is ___ than me.', ['tall', 'taller', 'tallest', 'more tall'], 2),
                $this->question('How ___ milk do you need?', ['many', 'much', 'few', 'little'], 2),
                $this->question('She has lived here ___ 2020.', ['for', 'since', 'from', 'during'], 2),
                $this->question('I ___ TV when you called.', ['watch', 'watched', 'was watching', 'am watching'], 3),
            ],
            'B1' => [
                $this->question('If it ___ tomorrow, we will stay home.', ['rain', 'rains', 'rained', 'raining'], 2),
                $this->question('The book ___ by a famous author.', ['wrote', 'was written', 'is writing', 'writes'], 2),
                $this->question('I look forward to ___ from you.', ['hear', 'hearing', 'heard', 'be heard'], 2),
                $this->question('He asked me where I ___.', ['live', 'lived', 'am living', 'have lived'], 2),
                $this->question('Despite ___ tired, she finished the project.', ['be', 'being', 'was', 'is'], 2),
            ],
            'B2' => [
                $this->question('By the time we arrived, the film ___.', ['started', 'had started', 'has started', 'starting'], 2),
                $this->question('Not only ___ late, but he also forgot the documents.', ['he arrived', 'did he arrive', 'he did arrive', 'arrived he'], 2),
                $this->question('The manager suggested that the report ___ revised.', ['is', 'be', 'was', 'being'], 2),
                $this->question('She speaks English fluently, ___?', ['does she', "doesn't she", 'is she', "isn't she"], 2),
                $this->question('The new policy will come ___ effect next month.', ['on', 'in', 'at', 'into'], 2),
            ],
            'C1' => [
                $this->question('The findings are ___ with previous research.', ['consistent', 'consist', 'consistency', 'consistently'], 1),
                $this->question('Had I known, I ___ differently.', ['would act', 'would have acted', 'acted', 'had acted'], 2),
                $this->question('The committee reached a ___ after lengthy discussion.', ['consensus', 'consent', 'consequence', 'concession'], 1),
                $this->question('His explanation was so ___ that nobody understood it.', ['clear', 'coherent', 'obscure', 'precise'], 3),
                $this->question('The proposal was rejected ___ its high cost.', ['because', 'although', 'despite', 'unless'], 3),
            ],
        ];
    }

    /**
     * @param list<string> $options
     * @return array{question_text: string, options: list<array{text: string, is_correct: bool}>}
     */
    private function question(string $questionText, array $options, int $correctPosition): array
    {
        return [
            'question_text' => $questionText,
            'options' => array_map(
                static fn (string $option, int $index): array => [
                    'text' => $option,
                    'is_correct' => $index + 1 === $correctPosition,
                ],
                $options,
                array_keys($options),
            ),
        ];
    }
}
