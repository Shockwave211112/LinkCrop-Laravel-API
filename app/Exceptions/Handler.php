<?php

namespace App\Exceptions;

use App\Modules\Core\Exceptions\AuthException;
use App\Modules\Core\Exceptions\DataBaseException;
use App\Modules\Core\Exceptions\EmailException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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

    /**
     * @param $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthException) {
            return response()->json(
                [
                    'message' => $exception->getMessage(),
                ],
                $exception->status
            );
        }
        if ($exception instanceof DataBaseException) {
            return response()->json(
                [
                    'message' => $exception->getMessage(),
                ],
                $exception->status
            );
        }
        if ($exception instanceof EmailException) {
            return response()->json(
                [
                    'message' => $exception->getMessage(),
                ],
                $exception->status
            );
        }
        return parent::render($request, $exception);
    }
}
