<?php

// This bootstrap intentionally refuses to use the application's configured DB.
$isolation = getenv('ALA_SCHOLARSHIP_TEST_DIR');
if (! is_string($isolation)
    || ! preg_match('~^/tmp/ala-scholarship-tests-[a-f0-9]{16}$~D', $isolation)
    || ! is_file($isolation.'/isolated-test-server')
    || ! file_exists($isolation.'/mysql.sock')) {
    throw new RuntimeException('Run scholarship tests with php tests/Scholarship/run-mariadb.php.');
}

$environment = [
    'APP_ENV' => 'testing',
    'APP_CONFIG_CACHE' => $isolation.'/unused-config-cache.php',
    'DB_CONNECTION' => 'mysql',
    'DB_URL' => '',
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_SOCKET' => $isolation.'/mysql.sock',
    'DB_DATABASE' => 'scholarship_domain_test',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => '',
    'CACHE_STORE' => 'array',
    'SESSION_DRIVER' => 'array',
    'MAIL_MAILER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'LOG_CHANNEL' => 'null',
    'BCRYPT_ROUNDS' => '4',
];
foreach ($environment as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $_SERVER[$key] = $value;
}

require_once dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

if (config('database.connections.mysql.unix_socket') !== $isolation.'/mysql.sock'
    || config('database.connections.mysql.database') !== 'scholarship_domain_test'
    || ! $app->environment('testing')) {
    throw new RuntimeException('Isolated scholarship database configuration was not applied.');
}

// Every process, including concurrent workers, uses the same deterministic clock.
Illuminate\Support\Carbon::setTestNow('2035-01-10 12:00:00');
Carbon\CarbonImmutable::setTestNow('2035-01-10 12:00:00');
