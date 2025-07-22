<?php

use App\Http\Middleware\isAdmin_middle;
use App\Http\Middleware\mid_language;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => isAdmin_middle::class
        ]);
        $middleware->web(append: [
            mid_language::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
