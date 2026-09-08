<?php

require __DIR__.'/bootstrap.php';

$request = json_decode($argv[1], true, flags: JSON_THROW_ON_ERROR);
$directory = getenv('ALA_SCHOLARSHIP_TEST_DIR');
$barrier = $directory.'/race-'.$request['race'];
touch($barrier.'-'.$request['worker'].'.ready');
$deadline = microtime(true) + 15;
while (! is_file($barrier.'.go')) {
    if (microtime(true) > $deadline) {
        throw new RuntimeException('Concurrency barrier timed out.');
    }
    usleep(10000);
}

try {
    $actor = App\Models\User::findOrFail($request['user_id']);
    $result = match ($request['operation'] ?? 'create') {
        'capacity' => ['capacity' => app(App\Services\ScholarshipCatalogService::class)->saveSession($actor, $request['data'], $request['session_id'])->capacity],
        'update' => ['updated' => app(App\Services\ScholarshipApplicationService::class)->update($actor, $request['application_id'], $request['data'])->id],
        default => ['created' => app(App\Services\ScholarshipApplicationService::class)->create($actor, $request['data'])->id],
    };
    echo json_encode($result, JSON_THROW_ON_ERROR);
} catch (Illuminate\Validation\ValidationException $e) {
    echo json_encode(['validation' => array_keys($e->errors())], JSON_THROW_ON_ERROR);
}
