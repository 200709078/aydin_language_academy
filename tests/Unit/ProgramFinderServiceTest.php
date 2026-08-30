<?php

use App\Services\ProgramFinderService;
use App\Support\FrontendReturnRoutes;

test('it recommends the matching school program for a student', function () {
    $recommendation = (new ProgramFinderService())->recommend([
        'learner_type' => 'student',
        'goal' => 'school_support',
        'school_stage' => 'primary',
        'self_level' => 'A2',
    ], null);

    expect($recommendation['primary']['route'])->toBe('frontend.courses.primary-school')
        ->and($recommendation['alternative'])->toBeNull()
        ->and($recommendation['level'])->toBe([
            'code' => 'A2',
            'source' => 'self_declaration',
        ]);
});

test('it gives a selected exam program priority and keeps the school program as an alternative', function () {
    $recommendation = (new ProgramFinderService())->recommend([
        'learner_type' => 'student',
        'goal' => 'exam',
        'school_stage' => 'high',
        'exam' => 'yks_dil',
        'self_level' => 'B1',
    ], null);

    expect($recommendation['primary']['route'])->toBe('frontend.courses.yks-dil')
        ->and($recommendation['alternative']['route'])->toBe('frontend.courses.high-school');
});

test('it uses an approved placement level and suggests speaking clubs as an adult alternative at advanced levels', function () {
    $recommendation = (new ProgramFinderService())->recommend([
        'learner_type' => 'adult',
        'goal' => 'general',
    ], 'B2');

    expect($recommendation['primary']['route'])->toBe('frontend.courses.general-english')
        ->and($recommendation['alternative']['route'])->toBe('frontend.courses.speaking-clubs')
        ->and($recommendation['level'])->toBe([
            'code' => 'B2',
            'source' => 'placement_test',
        ]);
});

test('it recommends speaking clubs when adult speaking practice is selected', function () {
    $recommendation = (new ProgramFinderService())->recommend([
        'learner_type' => 'adult',
        'goal' => 'speaking',
        'self_level' => 'unknown',
    ], null);

    expect($recommendation['primary']['route'])->toBe('frontend.courses.speaking-clubs')
        ->and($recommendation['alternative']['route'])->toBe('frontend.courses.general-english');
});

test('the program finder is an allowed frontend return route', function () {
    expect(FrontendReturnRoutes::resolve('frontend.program-finder'))->toBe('frontend.program-finder');
});
