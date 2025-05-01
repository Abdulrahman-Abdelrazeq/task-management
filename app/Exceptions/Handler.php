<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use App\Traits\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    use Response;

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

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle unauthenticated access attempts
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        // Handle model not found exceptions (e.g., when a Task is not found)
        if ($exception instanceof ModelNotFoundException) {
            $modelClass = $exception->getModel(); // Full class name, e.g. App\Models\Task
            $modelName = class_basename($modelClass); // Extracts "Task"
            $modelName = Str::headline(Str::snake($modelName));

            return $this->sendRes(false, "$modelName not found.", null, null, 404);
        }

        // Fall back to the default exception handler
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // If the request expects JSON, return a structured error response
        if ($request->expectsJson()) {
            return $this->sendRes(false, 'You are unauthenticated', null, null, 401);
        }

        // Otherwise, redirect to login page (typically for web routes)
        return redirect()->guest(route('login'));
    }
}
