<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Exceptions\NoContentsException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        NoContentsException::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
//        if ($e instanceof HttpException) {
//            // 403
//            if ($e->getStatusCode() == 403) {
//                return response()->json(['status' => '403'], 403);
//            }
//            // 404
//            if ($e->getStatusCode() == 404) {
//                return response()->json(['status' => '404'], 404);
//            }
//
//        } else if ($e instanceof NoContentsException) {
//            return response()->json(['status' => '204'], 204);
//        } else if ($e instanceof AuthorizationException) {
//            return response()->json(['status' => '401'], 401);
//        } else if ($e instanceof Exception) {
//            return response()->json(['status' => '500'], 500);
//        }
        if ($e instanceof NoContentsException) {
            return response()->json(['status' => '204'], 204);
        }
        return parent::render($request, $e);
    }
}
