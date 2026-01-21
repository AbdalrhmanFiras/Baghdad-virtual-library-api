<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log all exceptions safely
        $exceptions->reportable(function (\Throwable $e) {
            try {
                if (function_exists('error_log')) {
                    error_log('Laravel Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
                }
            } catch (\Throwable $logError) {
                // Silently fail if logging fails
            }
        });
    })->create();
