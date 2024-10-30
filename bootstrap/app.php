<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception) {

            if ($exception instanceof ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                    'errors' => $exception->errors(),

                ], 422);
            }

            if ($exception instanceof AuthorizationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                ], 403); // Forbidden
            }

            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                ], 401); // Unauthorized
            }


            if ($exception instanceof HttpException) {
                return response()->json([
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                ], $exception->getStatusCode());
            }

            // For all other exceptions
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                // Adjust for production to avoid leaking sensitive information
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ], 500); // Call the helper directly
        });
    })->create();
