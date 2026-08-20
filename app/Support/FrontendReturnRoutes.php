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
            'frontend.trainings.preschool' => 'frontend.trainings.preschool',
            'frontend.trainings.primary-school' => 'frontend.trainings.primary-school',
            'frontend.trainings.middle-school' => 'frontend.trainings.middle-school',
            'frontend.trainings.high-school' => 'frontend.trainings.high-school',
            'frontend.trainings.adults' => 'frontend.trainings.adults',
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
