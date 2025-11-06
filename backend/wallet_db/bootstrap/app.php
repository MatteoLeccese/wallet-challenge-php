<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SystemApiKeyMiddleware;
use App\Http\Middleware\ApiResponseMiddleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(ApiResponseMiddleware::class);
        $middleware->append(SystemApiKeyMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle validation exceptions
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'status'  => 400,
                'message' => 'An error occurred',
                'error'   => $e->getMessage() ?: 'Validation failed',
                'data'    => $e->errors(),
            ], 400);
        });

        // Handle all other exceptions
        $exceptions->render(function (Throwable $e, $request) {
            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;

            return response()->json([
                'status'  => $status,
                'message' => 'An error occurred',
                'error'   => $e->getMessage() ?: 'An unexpected error has occurred',
                'data'    => null,
            ], $status);
        });
    })->create();
