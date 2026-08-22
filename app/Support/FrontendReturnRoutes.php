<?php

namespace App\Support;

final class FrontendReturnRoutes
{
    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return [
            'home' => 'home',
            'frontend.achievements' => 'frontend.achievements',
            'frontend.campaigns' => 'frontend.campaigns',
            'frontend.placement-test' => 'frontend.placement-test',
            'frontend.trainings.preschool' => 'frontend.trainings.preschool',
            'frontend.trainings.primary-school' => 'frontend.trainings.primary-school',
            'frontend.trainings.middle-school' => 'frontend.trainings.middle-school',
            'frontend.trainings.high-school' => 'frontend.trainings.high-school',
            'frontend.trainings.ielts' => 'frontend.trainings.ielts',
            'frontend.trainings.yks-dil' => 'frontend.trainings.yks-dil',
            'frontend.trainings.yds-yokdil' => 'frontend.trainings.yds-yokdil',
            'frontend.trainings.toefl' => 'frontend.trainings.toefl',
            'frontend.trainings.pte-academic' => 'frontend.trainings.pte-academic',
            'frontend.trainings.test-of-english' => 'frontend.trainings.test-of-english',
            'frontend.trainings.sat' => 'frontend.trainings.sat',
            'frontend.trainings.general-english' => 'frontend.trainings.general-english',
            'frontend.trainings.speaking-clubs' => 'frontend.trainings.speaking-clubs',
            'frontend.branches.ortaca' => 'frontend.branches.ortaca',
            'frontend.branches.dalaman' => 'frontend.branches.dalaman',
            'frontend.branches.koycegiz' => 'frontend.branches.koycegiz',
            'frontend.preview.home' => 'frontend.preview.home',
        ];
    }

    public static function resolve(mixed $routeName): ?string
    {
        if (! is_string($routeName)) {
            return null;
        }

        return self::all()[$routeName] ?? null;
    }
}
