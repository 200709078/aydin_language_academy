<?php

namespace Tests\Scholarship;

use App\Models\ScholarshipApplication;
use App\Models\ScholarshipExamPeriod;
use Illuminate\Support\Facades\DB;

class ScholarshipConcurrencyTest extends ScholarshipTestCase
{
    public function test_two_students_cannot_take_the_final_seat(): void
    {
        $this->catalog->saveSession($this->admin, ['capacity' => 1], $this->session->id);
        $results = $this->race([
            ['user_id' => $this->student->id, 'data' => $this->data()],
            ['user_id' => $this->other->id, 'data' => $this->data()],
        ]);
        self::assertCount(1, array_filter($results, fn ($result) => isset($result['created'])));
        self::assertCount(1, array_filter($results, fn ($result) => isset($result['validation'])));
        self::assertSame(1, $this->session->applications()->count());
    }

    public function test_same_account_cannot_apply_at_two_branches_concurrently(): void
    {
        $results = $this->race([
            ['user_id' => $this->student->id, 'data' => $this->data()],
            ['user_id' => $this->student->id, 'data' => $this->data($this->alternative)],
        ]);
        self::assertCount(1, array_filter($results, fn ($result) => isset($result['created'])));
        self::assertCount(1, array_filter($results, fn ($result) => isset($result['validation'])));
        self::assertSame(1, $this->period->applications()->count());
    }

    public function test_capacity_reduction_racing_with_an_application_preserves_occupancy(): void
    {
        $this->applications->create($this->student, $this->data());
        $results = $this->race([
            ['user_id' => $this->other->id, 'data' => $this->data()],
            ['operation' => 'capacity', 'user_id' => $this->admin->id, 'session_id' => $this->session->id, 'data' => ['capacity' => 1]],
        ]);
        self::assertCount(1, array_filter($results, fn ($result) => isset($result['validation'])));
        self::assertLessThanOrEqual($this->session->fresh()->capacity, $this->session->applications()->count());
    }

    public function test_transfer_and_new_application_competing_for_one_seat_are_atomic(): void
    {
        $original = $this->applications->create($this->student, $this->data());
        $this->catalog->saveSession($this->admin, ['capacity' => 1], $this->alternative->id);
        $results = $this->race([
            ['operation' => 'update', 'user_id' => $this->admin->id, 'application_id' => $original->id, 'data' => ['session_id' => $this->alternative->id]],
            ['user_id' => $this->other->id, 'data' => $this->data($this->alternative)],
        ]);
        self::assertCount(1, array_filter($results, fn ($result) => isset($result['validation'])));
        self::assertSame(1, $this->alternative->applications()->count());
        self::assertNotNull($original->fresh());
        $moved = $original->fresh()->session_id === $this->alternative->id;
        self::assertSame($moved ? 0 : 1, $this->session->applications()->count());
    }

    public function test_write_commands_reject_an_ambient_transaction_without_changing_it(): void
    {
        DB::beginTransaction();
        try {
            self::assertSame(0, ScholarshipApplication::query()->count());
            // Worker commits after the parent's consistent-read snapshot exists.
            $results = $this->race([
                ['user_id' => $this->student->id, 'data' => $this->data()],
                ['user_id' => $this->other->id, 'data' => $this->data()],
            ], false);
            self::assertArrayHasKey('created', $results[0]);
            self::assertArrayHasKey('created', $results[1]);
            $this->rejects(fn () => $this->applications->create($this->third, $this->data()), \LogicException::class);
            $this->rejects(fn () => $this->catalog->saveSession($this->admin, ['capacity' => 1], $this->session->id), \LogicException::class);
            self::assertSame(1, DB::transactionLevel());
        } finally {
            DB::rollBack(0);
        }
        self::assertSame(2, $this->session->applications()->count());
    }

    /** Synchronize independent PHP/DB connections behind a held period lock. */
    private function race(array $requests, bool $holdPeriod = true): array
    {
        $directory = getenv('ALA_SCHOLARSHIP_TEST_DIR');
        $race = bin2hex(random_bytes(6));
        $barrier = $directory.'/race-'.$race;
        $workers = [];
        $locked = false;
        try {
            if ($holdPeriod) {
                DB::beginTransaction();
                ScholarshipExamPeriod::query()->whereKey($this->period->id)->lockForUpdate()->firstOrFail();
                $locked = true;
            }
            foreach ($requests as $number => $request) {
                $request += ['race' => $race, 'worker' => $number];
                $log = $barrier.'-'.$number.'.output';
                $process = proc_open([PHP_BINARY, __DIR__.'/concurrency-worker.php', json_encode($request, JSON_THROW_ON_ERROR)],
                    [0 => ['file', '/dev/null', 'r'], 1 => ['file', $log, 'w'], 2 => ['file', $log, 'a']], $pipes);
                self::assertIsResource($process);
                $workers[$number] = ['process' => $process, 'log' => $log, 'exit' => null];
            }
            $deadline = microtime(true) + 15;
            while (count(glob($barrier.'-*.ready')) !== count($workers)) {
                if (microtime(true) >= $deadline) {
                    self::fail('Concurrent workers did not reach the start barrier.');
                }
                usleep(10000);
            }
            touch($barrier.'.go');
            if ($locked) {
                usleep(150000);
                DB::commit();
                $locked = false;
            }
            do {
                $running = false;
                foreach ($workers as &$worker) {
                    if ($worker['exit'] === null) {
                        $status = proc_get_status($worker['process']);
                        if ($status['running']) {
                            $running = true;
                        } else {
                            $worker['exit'] = $status['exitcode'];
                        }
                    }
                }
                unset($worker);
                if ($running && microtime(true) >= $deadline) {
                    self::fail('Concurrent scholarship operation timed out.');
                }
                if ($running) {
                    usleep(10000);
                }
            } while ($running);
            $results = [];
            foreach ($workers as $worker) {
                $output = file_get_contents($worker['log']);
                self::assertSame(0, $worker['exit'], $output);
                $results[] = json_decode($output, true, flags: JSON_THROW_ON_ERROR);
            }

            return $results;
        } finally {
            if ($locked) {
                DB::rollBack();
            }
            foreach ($workers as $worker) {
                if (is_resource($worker['process'])) {
                    if (proc_get_status($worker['process'])['running']) {
                        proc_terminate($worker['process']);
                    }
                    proc_close($worker['process']);
                }
            }
        }
    }
}
