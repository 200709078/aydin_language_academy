<?php
namespace Database\Seeders;
use App\Models\PlacementTestLevel;
use Illuminate\Database\Seeder;

class PlacementTestLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['code' => 'A1', 'sequence' => 1, 'has_exam' => true, 'is_active' => true, 'question_count' => null, 'pass_percentage' => 60],
            ['code' => 'A2', 'sequence' => 2, 'has_exam' => true, 'is_active' => true, 'question_count' => null, 'pass_percentage' => 60],
            ['code' => 'B1', 'sequence' => 3, 'has_exam' => true, 'is_active' => true, 'question_count' => null, 'pass_percentage' => 60],
            ['code' => 'B2', 'sequence' => 4, 'has_exam' => true, 'is_active' => true, 'question_count' => null, 'pass_percentage' => 60],
            ['code' => 'C1', 'sequence' => 5, 'has_exam' => true, 'is_active' => true, 'question_count' => null, 'pass_percentage' => 60],
            ['code' => 'C2', 'sequence' => 6, 'has_exam' => false, 'is_active' => true, 'question_count' => 0, 'pass_percentage' => null],
        ];

        foreach ($levels as $level) {
            PlacementTestLevel::query()->firstOrCreate(
                ['code' => $level['code']],
                [
                    'sequence' => $level['sequence'],
                    'has_exam' => $level['has_exam'],
                    'is_active' => $level['is_active'],
                    'question_count' => $level['question_count'],
                    'pass_percentage' => $level['pass_percentage'],
                ],
            );
        }
    }
}
