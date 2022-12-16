<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
     * Convert an authentication exception into a response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Auth\AuthenticationException $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {

        if ($request->expectsJson()) {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }

    }

    /**
     * Render an exception into an HTTP response.
     * @param Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, \Throwable $e)
    {

        if ($request->expectsJson()) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json(['success' => false, 'message' => 'Resource not found', 'data' => null], 404);
            }
            if ($e instanceof NotFoundHttpException) {
                return response()->json(['success' => false, 'message' => 'Resource not found', 'data' => null], 404);
            }
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null], 405);
            }

            if ($e instanceof UnauthorizedHttpException) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null, 'code' => 401], 401);
            }

            if ($e instanceof AuthorizationException) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null, 'code' => 401], 401);
            }

            if ($e instanceof HttpException) {
                return response()->json(['success' => false, 'message' => $e->getMessage(), 'data' => null, 'code' => 403], 403);
            }
        }

        return parent::render($request, $e);
    }
}
