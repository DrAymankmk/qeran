<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Avoid cascading "A facade root has not been set" when bootstrap failed early.
     */
    protected function registerErrorViewPaths()
    {
        if (! $this->container->bound('view')) {
            return;
        }

        parent::registerErrorViewPaths();
    }

    /**
     * When the app did not finish booting, return plain text instead of crashing again.
     */
    protected function renderExceptionResponse($request, Throwable $e)
    {
        if (! $this->container->bound('view')) {
            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $debug = $this->container->bound('config')
                ? (bool) $this->container->make('config')->get('app.debug')
                : false;

            $message = $debug
                ? $e->getMessage()."\n\n".$e->getTraceAsString()
                : 'Service Unavailable';

            return response($message, $status, [
                'Content-Type' => 'text/plain; charset=UTF-8',
            ]);
        }

        return parent::renderExceptionResponse($request, $e);
    }
}
