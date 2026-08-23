<?php
namespace Database\Seeders;
use App\Models\PlacementTest;
use App\Models\PlacementTestLevel;
use App\Models\PlacementTestLevelQuestion;
use App\Models\PlacementTestLevelResult;
use App\Models\PlacementTestLevelResultContent;
use App\Models\PlacementTestQuestion;
use App\Models\PlacementTestQuestionContent;
use App\Models\PlacementTestQuestionOption;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LogicException;

class PlacementTestDemoSeeder extends Seeder
{
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

        $this->seedQuestionBank($levels);
        $this->seedApprovedA2Attempt($studentA2, $admin, $levels);
        $this->seedPendingC2Attempt($studentC2, $levels);
        $this->seedInProgressAttempt($studentProgress, $levels);
    }

    /**
     * @param  array{name: string, email: string, phone: string, type: string}  $attributes
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
     * @param  \Illuminate\Support\Collection<string, PlacementTestLevel>  $levels
     */
    private function seedQuestionBank($levels): void
    {
        foreach ($this->questionBank() as $levelCode => $definition) {
            $level = $levels->get($levelCode);

            if ($level === null) {
                throw new LogicException("Demo level {$levelCode} bulunamadı.");
            }

            $contentsByKey = [];

            foreach ($definition['contents'] as $key => $contentData) {
                $contentsByKey[$key] = PlacementTestQuestionContent::query()->firstOrCreate(
                    [
                        'placement_test_level_id' => $level->id,
                        'type' => $contentData['type'],
                        'text_content' => $contentData['text_content'],
                    ],
                    [
                        'media_disk' => null,
                        'media_path' => null,
                        'is_active' => true,
                    ],
                );
            }

            foreach ($definition['questions'] as $questionData) {
                $content = $questionData['content_key'] === null
                    ? null
                    : $contentsByKey[$questionData['content_key']];

                $question = PlacementTestQuestion::query()->firstOrCreate(
                    [
                        'placement_test_level_id' => $level->id,
                        'question_text' => $questionData['question_text'],
                    ],
                    [
                        'placement_test_question_content_id' => $content?->id,
                        'content_position' => $questionData['content_position'],
                        'points' => $questionData['points'],
                        'is_active' => true,
                    ],
                );

                foreach ($questionData['options'] as $position => $option) {
                    PlacementTestQuestionOption::query()->firstOrCreate(
                        [
                            'placement_test_question_id' => $question->id,
                            'display_position' => $position + 1,
                        ],
                        [
                            'option_text' => $option['text'],
                            'is_correct' => $option['is_correct'],
                        ],
                    );
                }
            }
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<string, PlacementTestLevel>  $levels
     */
    private function seedApprovedA2Attempt(User $student, User $admin, $levels): void
    {
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
            ['correct', 'correct', 'correct', 'wrong', 'blank'],
            $startedAt,
            CarbonImmutable::parse('2026-01-10 09:12:00'),
        );
        $this->seedLevelResult(
            $test,
            $levels->get('A2'),
            ['correct', 'correct', 'wrong', 'wrong', 'blank'],
            CarbonImmutable::parse('2026-01-10 09:13:00'),
            CarbonImmutable::parse('2026-01-10 09:28:00'),
        );
    }

    /**
     * @param  \Illuminate\Support\Collection<string, PlacementTestLevel>  $levels
     */
    private function seedPendingC2Attempt(User $student, $levels): void
    {
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
                ['correct', 'correct', 'correct', 'wrong', 'blank'],
                $levelStartedAt,
                $levelStartedAt->addMinutes(14),
            );
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<string, PlacementTestLevel>  $levels
     */
    private function seedInProgressAttempt(User $student, $levels): void
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
            $levels->get('A1'),
            ['correct', 'wrong', 'blank', 'blank', 'blank'],
            $startedAt,
            null,
        );
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

    /**
     * @return array<string, array{contents: array<string, array{type: string, text_content: string}>, questions: list<array{question_text: string, options: list<array{text: string, is_correct: bool}>, points: string, content_key: ?string, content_position: ?int}>}>
     */
    private function questionBank(): array
    {
        return [
            'A1' => [
                'contents' => [
                    'a1-reading-1' => [
                        'type' => 'text',
                        'text_content' => 'Read the text: Ayşe is eleven years old. She lives in Ortaca with her family. Every Saturday, she visits the library and borrows a book.',
                    ],
                ],
                'questions' => [
                    $this->question('How old is Ayşe?', ['Eleven', 'Twelve', 'Thirteen', 'Ten'], 1, '2.50', 'a1-reading-1', 1),
                    $this->question('Where does Ayşe go every Saturday?', ['To the park', 'To the library', 'To school', 'To the beach'], 2, '2.50', 'a1-reading-1', 2),
                    $this->question('I ___ a student.', ['am', 'is', 'are', 'be'], 1, '1.00'),
                    $this->question('Choose the correct article: ___ apple.', ['A', 'An', 'The', 'No article'], 2, '1.50'),
                    $this->question('There ___ three books on the table.', ['are', 'is', 'am', 'be'], 1, '2.50'),
                ],
            ],
            'A2' => [
                'contents' => [
                    'a2-reading-1' => [
                        'type' => 'text',
                        'text_content' => 'Read the text: Last Saturday, Emre travelled to Dalaman by bus. He met his cousin, had lunch near the marina, and returned home in the evening.',
                    ],
                ],
                'questions' => [
                    $this->question('Where did Emre travel last Saturday?', ['To Ortaca', 'To Dalaman', 'To Muğla', 'To İzmir'], 2, '2.50', 'a2-reading-1', 1),
                    $this->question('How did Emre travel?', ['By car', 'By train', 'By bus', 'By plane'], 3, '2.50', 'a2-reading-1', 2),
                    $this->question('Yesterday, we ___ to the park.', ['go', 'went', 'gone', 'going'], 2, '1.00'),
                    $this->question('My brother is ___ than me.', ['tall', 'taller', 'tallest', 'more tall'], 2, '1.50'),
                    $this->question('I ___ TV when you called.', ['watch', 'watched', 'was watching', 'am watching'], 3, '2.50'),
                ],
            ],
            'B1' => [
                'contents' => [
                    'b1-reading-1' => [
                        'type' => 'text',
                        'text_content' => 'Read the text: Deniz joined the school environmental club because she wants to protect the local beach. The club is organising a clean-up event for Saturday morning.',
                    ],
                ],
                'questions' => [
                    $this->question('Why did Deniz join the club?', ['To make new uniforms', 'To protect the local beach', 'To travel abroad', 'To learn to swim'], 2, '2.50', 'b1-reading-1', 1),
                    $this->question('When is the clean-up event?', ['Saturday morning', 'Friday evening', 'Sunday afternoon', 'Monday morning'], 1, '2.50', 'b1-reading-1', 2),
                    $this->question('If it ___ tomorrow, we will stay home.', ['rain', 'rains', 'rained', 'raining'], 2, '1.00'),
                    $this->question('The book ___ by a famous author.', ['wrote', 'was written', 'is writing', 'writes'], 2, '1.50'),
                    $this->question('I look forward to ___ from you.', ['hear', 'hearing', 'heard', 'be heard'], 2, '2.50'),
                ],
            ],
            'B2' => [
                'contents' => [
                    'b2-reading-1' => [
                        'type' => 'text',
                        'text_content' => 'Read the text: A company introduced a remote-work policy for three months. Its report found that productivity remained stable, while most employees valued the additional flexibility.',
                    ],
                ],
                'questions' => [
                    $this->question('What happened to productivity?', ['It increased sharply', 'It remained stable', 'It stopped completely', 'It was not measured'], 2, '2.50', 'b2-reading-1', 1),
                    $this->question('What did most employees value?', ['A shorter report', 'Additional flexibility', 'New uniforms', 'Longer meetings'], 2, '2.50', 'b2-reading-1', 2),
                    $this->question('By the time we arrived, the film ___.', ['started', 'had started', 'has started', 'starting'], 2, '1.00'),
                    $this->question('Not only ___ late, but he also forgot the documents.', ['he arrived', 'did he arrive', 'he did arrive', 'arrived he'], 2, '1.50'),
                    $this->question('The manager suggested that the report ___ revised.', ['is', 'be', 'was', 'being'], 2, '2.50'),
                ],
            ],
            'C1' => [
                'contents' => [
                    'c1-reading-1' => [
                        'type' => 'text',
                        'text_content' => 'Read the text: Researchers argue that urban green spaces are not merely decorative. When planned well, they can reduce heat, support biodiversity, and give residents places to recover from daily stress.',
                    ],
                ],
                'questions' => [
                    $this->question('What is the central claim of the text?', ['Green spaces are too expensive', 'Green spaces have practical benefits', 'Cities should remove parks', 'Only biodiversity matters'], 2, '2.50', 'c1-reading-1', 1),
                    $this->question('Which benefit is mentioned?', ['Reducing heat', 'Increasing traffic', 'Replacing homes', 'Extending work hours'], 1, '2.50', 'c1-reading-1', 2),
                    $this->question('The findings are ___ with previous research.', ['consistent', 'consist', 'consistency', 'consistently'], 1, '1.00'),
                    $this->question('Had I known, I ___ differently.', ['would act', 'would have acted', 'acted', 'had acted'], 2, '1.50'),
                    $this->question('The committee reached a ___ after lengthy discussion.', ['consensus', 'consent', 'consequence', 'concession'], 1, '2.50'),
                ],
            ],
        ];
    }

    /**
     * @param  list<string>  $options
     * @return array{question_text: string, options: list<array{text: string, is_correct: bool}>, points: string, content_key: ?string, content_position: ?int}
     */
    private function question(
        string $questionText,
        array $options,
        int $correctPosition,
        string $points,
        ?string $contentKey = null,
        ?int $contentPosition = null,
    ): array {
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
            'points' => $points,
            'content_key' => $contentKey,
            'content_position' => $contentPosition,
        ];
    }
}
