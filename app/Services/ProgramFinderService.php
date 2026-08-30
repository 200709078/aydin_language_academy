<?php

namespace App\Services;

final class ProgramFinderService
{
    /**
     * @var array<string, array{route: string, title_key: string, description_key: string, icon: string}>
     */
    private const COURSES = [
        'preschool' => [
            'route' => 'frontend.courses.preschool',
            'title_key' => 'dictt.preschool',
            'description_key' => 'dictt.program_finder_course_preschool',
            'icon' => 'fa-child',
        ],
        'primary-school' => [
            'route' => 'frontend.courses.primary-school',
            'title_key' => 'dictt.primary_school',
            'description_key' => 'dictt.program_finder_course_primary_school',
            'icon' => 'fa-pencil-alt',
        ],
        'middle-school' => [
            'route' => 'frontend.courses.middle-school',
            'title_key' => 'dictt.middle_school',
            'description_key' => 'dictt.program_finder_course_middle_school',
            'icon' => 'fa-book-open',
        ],
        'high-school' => [
            'route' => 'frontend.courses.high-school',
            'title_key' => 'dictt.high_school',
            'description_key' => 'dictt.program_finder_course_high_school',
            'icon' => 'fa-graduation-cap',
        ],
        'general-english' => [
            'route' => 'frontend.courses.general-english',
            'title_key' => 'dictt.general_english',
            'description_key' => 'dictt.program_finder_course_general_english',
            'icon' => 'fa-comments',
        ],
        'speaking-clubs' => [
            'route' => 'frontend.courses.speaking-clubs',
            'title_key' => 'dictt.speaking_clubs',
            'description_key' => 'dictt.program_finder_course_speaking_clubs',
            'icon' => 'fa-users',
        ],
        'ielts' => [
            'route' => 'frontend.courses.ielts',
            'title_key' => 'dictt.ielts_prep',
            'description_key' => 'dictt.program_finder_course_ielts',
            'icon' => 'fa-plane-departure',
        ],
        'yks-dil' => [
            'route' => 'frontend.courses.yks-dil',
            'title_key' => 'dictt.yks_dil_prep',
            'description_key' => 'dictt.program_finder_course_yks_dil',
            'icon' => 'fa-university',
        ],
        'yds-yokdil' => [
            'route' => 'frontend.courses.yds-yokdil',
            'title_key' => 'dictt.yds_yokdil',
            'description_key' => 'dictt.program_finder_course_yds_yokdil',
            'icon' => 'fa-book',
        ],
        'toefl' => [
            'route' => 'frontend.courses.toefl',
            'title_key' => 'dictt.toefl',
            'description_key' => 'dictt.program_finder_course_toefl',
            'icon' => 'fa-globe',
        ],
        'pte-academic' => [
            'route' => 'frontend.courses.pte-academic',
            'title_key' => 'dictt.pte_academic',
            'description_key' => 'dictt.program_finder_course_pte_academic',
            'icon' => 'fa-laptop',
        ],
        'test-of-english' => [
            'route' => 'frontend.courses.test-of-english',
            'title_key' => 'dictt.test_of_english',
            'description_key' => 'dictt.program_finder_course_test_of_english',
            'icon' => 'fa-check-circle',
        ],
        'sat' => [
            'route' => 'frontend.courses.sat',
            'title_key' => 'dictt.sat',
            'description_key' => 'dictt.program_finder_course_sat',
            'icon' => 'fa-calculator',
        ],
    ];

    /**
     * @var array<string, string>
     */
    private const SCHOOL_STAGE_COURSES = [
        'preschool' => 'preschool',
        'primary' => 'primary-school',
        'middle' => 'middle-school',
        'high' => 'high-school',
    ];

    /**
     * @var array<string, string>
     */
    private const EXAM_COURSES = [
        'ielts' => 'ielts',
        'yks_dil' => 'yks-dil',
        'yds_yokdil' => 'yds-yokdil',
        'toefl' => 'toefl',
        'pte_academic' => 'pte-academic',
        'test_of_english' => 'test-of-english',
        'sat' => 'sat',
    ];

    /**
     * @return list<string>
     */
    public function learnerTypes(): array
    {
        return ['student', 'adult'];
    }

    /**
     * @return list<string>
     */
    public function goals(): array
    {
        return ['school_support', 'general', 'speaking', 'exam'];
    }

    /**
     * @return list<string>
     */
    public function goalsForLearner(string $learnerType): array
    {
        return match ($learnerType) {
            'student' => ['school_support', 'general', 'speaking', 'exam'],
            'adult' => ['general', 'speaking', 'exam'],
            default => [],
        };
    }

    /**
     * @return list<string>
     */
    public function schoolStages(): array
    {
        return array_keys(self::SCHOOL_STAGE_COURSES);
    }

    /**
     * @return list<string>
     */
    public function exams(): array
    {
        return array_keys(self::EXAM_COURSES);
    }

    /**
     * @return list<string>
     */
    public function levels(): array
    {
        return ['unknown', 'A1', 'A2', 'B1', 'B2', 'C1', 'C2'];
    }

    /**
     * @param  array{learner_type: string, goal: string, school_stage?: string|null, exam?: string|null, self_level?: string|null}  $answers
     * @return array{
     *     primary: array{route: string, title_key: string, description_key: string, icon: string},
     *     alternative: array{route: string, title_key: string, description_key: string, icon: string}|null,
     *     level: array{code: string, source: 'placement_test'|'self_declaration'}
     * }
     */
    public function recommend(array $answers, ?string $placementLevelCode): array
    {
        $levelCode = $placementLevelCode ?? ($answers['self_level'] ?? 'unknown');
        $levelSource = $placementLevelCode === null ? 'self_declaration' : 'placement_test';

        if ($answers['goal'] === 'exam') {
            $primary = $this->courseForExam((string) ($answers['exam'] ?? ''));
            $alternative = $answers['learner_type'] === 'student'
                ? $this->courseForSchoolStage((string) ($answers['school_stage'] ?? ''))
                : $this->course('general-english');
        } elseif ($answers['learner_type'] === 'student') {
            $primary = $this->courseForSchoolStage((string) ($answers['school_stage'] ?? ''));
            $alternative = null;
        } elseif ($answers['goal'] === 'speaking') {
            $primary = $this->course('speaking-clubs');
            $alternative = $this->course('general-english');
        } else {
            $primary = $this->course('general-english');
            $alternative = in_array($levelCode, ['B2', 'C1', 'C2'], true)
                ? $this->course('speaking-clubs')
                : null;
        }

        return [
            'primary' => $primary,
            'alternative' => $alternative,
            'level' => [
                'code' => $levelCode,
                'source' => $levelSource,
            ],
        ];
    }

    /**
     * @return array{route: string, title_key: string, description_key: string, icon: string}
     */
    private function courseForSchoolStage(string $schoolStage): array
    {
        return $this->course(self::SCHOOL_STAGE_COURSES[$schoolStage] ?? 'high-school');
    }

    /**
     * @return array{route: string, title_key: string, description_key: string, icon: string}
     */
    private function courseForExam(string $exam): array
    {
        return $this->course(self::EXAM_COURSES[$exam] ?? 'general-english');
    }

    /**
     * @return array{route: string, title_key: string, description_key: string, icon: string}
     */
    private function course(string $course): array
    {
        return self::COURSES[$course];
    }
}
