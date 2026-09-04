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
            'frontend.program-finder' => 'frontend.program-finder',
            'frontend.courses.preschool' => 'frontend.courses.preschool',
            'frontend.courses.primary-school' => 'frontend.courses.primary-school',
            'frontend.courses.middle-school' => 'frontend.courses.middle-school',
            'frontend.courses.high-school' => 'frontend.courses.high-school',
            'frontend.courses.ielts' => 'frontend.courses.ielts',
            'frontend.courses.yks-dil' => 'frontend.courses.yks-dil',
            'frontend.courses.yds-yokdil' => 'frontend.courses.yds-yokdil',
            'frontend.courses.toefl' => 'frontend.courses.toefl',
            'frontend.courses.pte-academic' => 'frontend.courses.pte-academic',
            'frontend.courses.test-of-english' => 'frontend.courses.test-of-english',
            'frontend.courses.sat' => 'frontend.courses.sat',
            'frontend.courses.general-english' => 'frontend.courses.general-english',
            'frontend.courses.speaking-clubs' => 'frontend.courses.speaking-clubs',
            'frontend.branches' => 'frontend.branches',
            'frontend.reviews' => 'frontend.reviews',
            'frontend.my-reviews' => 'frontend.my-reviews',
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
