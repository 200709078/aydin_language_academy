<?php

namespace Database\Seeders;

use App\Models\model_exercises;
use App\Models\model_questions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

class ExerciseQuestionDemoSeeder extends Seeder
{
    private const DEMO_QUESTION_COUNT = 100;

    /**
     * Rebuild only the legacy exercise-question demo data.
     *
     * Placement Test questions use different tables and are intentionally not
     * read, changed, or seeded here.
     */
    public function run(): void
    {
        $exerciseIds = model_exercises::query()
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if ($exerciseIds === []) {
            throw new LogicException('Alıştırma demo soruları için önce en az bir alıştırma oluşturulmalıdır.');
        }

        DB::transaction(function () use ($exerciseIds): void {
            // question_options.question_id is cascade-on-delete, so only legacy
            // options belonging to these demo questions are removed as well.
            model_questions::query()->delete();

            model_questions::factory()
                ->count(self::DEMO_QUESTION_COUNT)
                ->state(fn (): array => [
                    'exercise_id' => $exerciseIds[array_rand($exerciseIds)],
                ])
                ->create();
        });
    }
}
