<?php

/** Run only scholarship domain tests on an ephemeral, socket-only MariaDB. */
$root = dirname(__DIR__, 2);
$directory = '/tmp/ala-scholarship-tests-'.bin2hex(random_bytes(8));
mkdir($directory, 0700);
file_put_contents($directory.'/isolated-test-server', 'scholarship-domain-tests');
putenv('ALA_SCHOLARSHIP_TEST_DIR='.$directory);
$server = null;
$connection = null;
$exitCode = 1;

/** @param list<string> $command */
function scholarshipTestProcess(array $command, string $log, int $timeout = 60): int
{
    $process = proc_open($command, [0 => ['file', '/dev/null', 'r'], 1 => ['file', $log, 'a'], 2 => ['file', $log, 'a']], $pipes);
    if (! is_resource($process)) {
        throw new RuntimeException('Could not start isolated test process.');
    }
    $deadline = microtime(true) + $timeout;
    do {
        $status = proc_get_status($process);
        if (! $status['running']) {
            proc_close($process);

            return $status['exitcode'];
        }
        usleep(50000);
    } while (microtime(true) < $deadline);
    proc_terminate($process);
    proc_close($process);
    throw new RuntimeException('Isolated test process timed out.');
}

try {
    if (! is_executable('/usr/sbin/mariadbd') || ! is_executable('/usr/bin/mariadb-install-db')) {
        throw new RuntimeException('MariaDB server tools are required: mariadbd and mariadb-install-db.');
    }
    $install = scholarshipTestProcess([
        '/usr/bin/mariadb-install-db', '--no-defaults', '--datadir='.$directory.'/data',
        '--auth-root-authentication-method=normal', '--skip-test-db',
    ], $directory.'/install.log');
    if ($install !== 0) {
        throw new RuntimeException('Isolated MariaDB initialization failed: '.file_get_contents($directory.'/install.log'));
    }
    $server = proc_open([
        '/usr/sbin/mariadbd', '--no-defaults', '--datadir='.$directory.'/data',
        '--socket='.$directory.'/mysql.sock', '--pid-file='.$directory.'/server.pid',
        '--skip-networking', '--skip-log-bin', '--innodb-buffer-pool-size=64M',
        '--log-error='.$directory.'/server.log',
    ], [0 => ['file', '/dev/null', 'r'], 1 => ['file', $directory.'/server-output.log', 'a'], 2 => ['file', $directory.'/server-output.log', 'a']], $pipes);
    if (! is_resource($server)) {
        throw new RuntimeException('Could not start isolated MariaDB server.');
    }
    $deadline = microtime(true) + 20;
    do {
        try {
            $connection = new PDO('mysql:unix_socket='.$directory.'/mysql.sock', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            break;
        } catch (PDOException) {
            usleep(100000);
        }
    } while (microtime(true) < $deadline);
    if ($connection === null) {
        throw new RuntimeException('Isolated MariaDB server did not become ready.');
    }
    $connection->exec('CREATE DATABASE scholarship_domain_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    require __DIR__.'/bootstrap.php';

    Illuminate\Support\Facades\Schema::create('users', function (Illuminate\Database\Schema\Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        $table->string('password');
        $table->string('type')->default('user');
        $table->timestamp('email_verified_at')->nullable();
        $table->rememberToken();
        $table->timestamps();
    });
    foreach ([
        '2026_09_07_100000_create_scholarship_reference_tables.php',
        '2026_09_07_100100_create_scholarship_exam_tables.php',
        '2026_09_07_100200_create_scholarship_application_tables.php',
    ] as $migration) {
        (require $root.'/database/migrations/'.$migration)->up();
    }
    echo "Isolated MariaDB initialized; running scholarship domain tests.\n";
    $process = proc_open([
        PHP_BINARY, $root.'/vendor/phpunit/phpunit/phpunit', '--no-configuration',
        '--bootstrap', __DIR__.'/bootstrap.php', '--do-not-cache-result', '--colors=never',
        __DIR__,
    ], [0 => ['file', '/dev/null', 'r'], 1 => STDOUT, 2 => STDERR], $pipes, $root);
    if (! is_resource($process)) {
        throw new RuntimeException('Could not start PHPUnit.');
    }
    $exitCode = proc_close($process);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage()."\n");
} finally {
    if (isset($app)) {
        Illuminate\Support\Facades\DB::disconnect();
    }
    if ($connection !== null) {
        try {
            $connection->exec('SHUTDOWN');
        } catch (PDOException) {
            // A terminated test server is already isolated and ready for cleanup.
        }
        $connection = null;
    }
    if (is_resource($server)) {
        $deadline = microtime(true) + 10;
        while (proc_get_status($server)['running'] && microtime(true) < $deadline) {
            usleep(100000);
        }
        if (proc_get_status($server)['running']) {
            proc_terminate($server);
        }
        proc_close($server);
    }
    // Only the randomly generated directory created by this invocation is removed.
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        $file->isDir() && ! $file->isLink() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($directory);
}
exit($exitCode);
