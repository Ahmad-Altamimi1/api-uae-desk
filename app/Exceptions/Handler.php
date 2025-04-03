<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    public function render($request, Throwable $exception)
    {
        // Handle unauthorized permission or role
        if ($exception instanceof UnauthorizedException) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 403); // Forbidden status
        }

        // Handle unauthenticated user (Passport issue, for example)
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401); // Unauthorized status
        }

        // Default response for all other exceptions when API expects JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $this->isHttpException($exception) ? $exception->getStatusCode() : 500);
        }

        // Default behavior for non-API requests
        return parent::render($request, $exception);
    }
}
