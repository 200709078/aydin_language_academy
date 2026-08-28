<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use LogicException;

class PlacementTestMasterDataSeeder extends Seeder
{
    /**
     * @var array<string, array{file: string, expected_count: int}>
     */
    private const TABLES = [
        'placement_test_levels' => [
            'file' => 'placement_test_levels.sql',
            'expected_count' => 6,
        ],
        'placement_test_question_contents' => [
            'file' => 'placement_test_question_contents.sql',
            'expected_count' => 15,
        ],
        'placement_test_questions' => [
            'file' => 'placement_test_questions.sql',
            'expected_count' => 103,
        ],
        'placement_test_question_options' => [
            'file' => 'placement_test_question_options.sql',
            'expected_count' => 412,
        ],
    ];

    public function run(): void
    {
        $counts = [];

        foreach (self::TABLES as $table => $definition) {
            $counts[$table] = DB::table($table)->count();
        }

        if (array_sum($counts) === 0) {
            DB::transaction(function (): void {
                foreach (self::TABLES as $definition) {
                    $path = database_path('seeders/data/' . $definition['file']);
                    $sql = file_get_contents($path);

                    if ($sql === false) {
                        throw new LogicException("Sınav master seed verisi okunamadı: {$definition['file']}");
                    }

                    DB::unprepared($sql);
                }
            });

            return;
        }

        foreach (self::TABLES as $table => $definition) {
            if ($counts[$table] !== $definition['expected_count']) {
                throw new LogicException('Sınav master tabloları kısmen dolu. Bu demo verisini temiz bir veritabanında çalıştırın.');
            }
        }
    }
}
