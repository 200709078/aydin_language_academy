<?php

namespace Database\Seeders;

use App\Models\ScholarshipBranch;
use App\Models\ScholarshipExamGroup;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipExamSession;
use App\Models\ScholarshipSchool;
use App\Models\ScholarshipStudentLevel;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

class ScholarshipDemoSeeder extends Seeder
{
    private const PERIOD_TITLE = 'Örnek Bursluluk Sınav Dönemi (Geliştirme)';

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('Bursluluk örnek verileri yalnız local veya testing ortamında oluşturulabilir.');
        }

        DB::transaction(function (): void {
            // Seed only an unused scholarship catalog. A renamed demo period
            // must not cause a later run to recreate definitions or sessions.
            if (ScholarshipExamPeriod::query()->exists()) {
                return;
            }

            $today = CarbonImmutable::now(config('app.timezone'))->startOfDay();
            $examDate = $today->addDays(30);
            $period = ScholarshipExamPeriod::query()->firstOrCreate(
                ['title' => self::PERIOD_TITLE],
                [
                    'description' => 'Geliştirme için örnek dönemdir. Gerçek başvuru kabul etmez; yönetici incelemesi için kapalı oluşturulur.',
                    'applications_open_at' => $today->addDay()->setTime(9, 0),
                    'applications_close_at' => $examDate->subDay()->setTime(18, 0),
                    'exam_starts_on' => $examDate,
                    'exam_ends_on' => $examDate->addDay(),
                    'is_active' => false,
                    'applications_open' => false,
                ]
            );

            // A seeded period marks a completed run; preserve subsequent admin edits and deletions.
            if (! $period->wasRecentlyCreated) {
                return;
            }

            $branches = [];

            foreach (['ortaca' => 'Ortaca', 'dalaman' => 'Dalaman', 'koycegiz' => 'Köyceğiz'] as $code => $name) {
                $branches[$code] = ScholarshipBranch::query()->firstOrCreate(
                    ['code' => $code],
                    ['name' => $name, 'sort_order' => count($branches)]
                );
            }

            foreach (['Örnek Öğrenci Okulu 1 (Geliştirme)', 'Örnek Öğrenci Okulu 2 (Geliştirme)'] as $sortOrder => $name) {
                ScholarshipSchool::query()->firstOrCreate(['name' => $name], ['sort_order' => $sortOrder]);
            }

            foreach (range(1, 12) as $grade) {
                ScholarshipStudentLevel::query()->firstOrCreate(
                    ['code' => 'grade-'.$grade],
                    ['name' => $grade.'. Sınıf', 'sort_order' => $grade]
                );
            }

            foreach (['graduate' => 'Mezun', 'yks' => 'YKS'] as $code => $name) {
                ScholarshipStudentLevel::query()->firstOrCreate(
                    ['code' => $code],
                    ['name' => $name, 'sort_order' => $code === 'graduate' ? 13 : 14]
                );
            }

            $groups = [];

            foreach (['primary' => 'İlkokul', 'middle' => 'Ortaokul', 'high' => 'Lise', 'graduate' => 'Mezun'] as $code => $name) {
                $groups[$code] = ScholarshipExamGroup::query()->firstOrCreate(
                    ['code' => $code],
                    ['name' => $name, 'sort_order' => count($groups)]
                );
            }

            $times = [
                ['08:00:00', '10:00:00'],
                ['10:00:00', '12:00:00'],
                ['16:00:00', '19:00:00'],
            ];

            foreach (['ortaca' => [20, 20, 20], 'dalaman' => [15, 25, 20], 'koycegiz' => [10, 20, 30]] as $code => $capacities) {
                foreach ($times as $index => [$startsAt, $endsAt]) {
                    ScholarshipExamSession::query()->create([
                        'period_id' => $period->id,
                        'branch_id' => $branches[$code]->id,
                        'exam_group_id' => $groups['high']->id,
                        'exam_title' => 'Örnek Bursluluk Sınavı (Geliştirme)',
                        'exam_date' => $examDate,
                        'starts_at' => $startsAt,
                        'ends_at' => $endsAt,
                        'capacity' => $capacities[$index],
                    ]);
                }
            }
        });
    }
}
