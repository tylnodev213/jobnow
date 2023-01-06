<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Session\TokenMismatchException;
use Throwable;
use PDOException;

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
        HttpException::class,
        TokenMismatchException::class,
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
     * @param Throwable $e
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        try {
            if ($this->shouldReport($e)) {
                $message = str($e->getMessage() . PHP_EOL . $e->getTraceAsString())->append('","[F]' . $e->getFile() . '",[L]' . $e->getLine());
                logError($message);
            }
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
            throw $exception;
        }
    }

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

    public function render($request, Throwable $e)
    {
        // CSRF Token Expires
        if ($e instanceof TokenMismatchException) {
            return redirect(getRoute('login'))
                ->with(['token_expiration' => __('messages.token_expiration')])
                ->withInput($request->except('_token'));
        }

        return parent::render($request, $e);
    }

    protected function convertExceptionToResponse(Throwable $e)
    {
        $area = getArea();
        if (config('app.debug') && 'api' != $area) {
            return parent::convertExceptionToResponse($e);
        }

        $msg = match (true) {
            $e instanceof PDOException => __('messages.db_not_connect'),
            default => __('messages.system_error'),
        };

        if ('api' == $area) {
            return response()->json(['status' => false, 'message' => $msg, 'data' => []]);
        }

        if (view()->exists("{$area}.errors.500")) {
            return response()->view("{$area}.errors.500", [
                'exception' => $e,
                'area' => $area,
                'title' => __('messages.page_title.errors'),
            ]);
        }

        return parent::convertExceptionToResponse($e);
    }

    protected function renderHttpException(HttpExceptionInterface $e)
    {
        $status = $e->getStatusCode();
        $area = getArea();

        if ('api' == $area) {
            $code = match (true) {
                $e instanceof NotFoundHttpException => getConstant('HTTP_CODE.NOT_FOUND'),
                $e instanceof MethodNotAllowedHttpException => getConstant('HTTP_CODE.METHOD_NOT_ALLOWED'),
                default => getConstant('HTTP_CODE.SERVER_ERROR'),
            };

            return response()->json([
                'status' => false,
                'message' => !empty($code) ? data_get(__('messages.http_code'), $code) : $e->getMessage(),
                'data' => [],
            ]);
        }

        if (view()->exists("{$area}.errors.{$status}")) {
            $data = [
                'exception' => $e,
                'area' => $area,
                'title' => __('messages.page_title.errors'),
            ];
            return response()->view("{$area}.errors.{$status}", $data);
        }

        return $this->convertExceptionToResponse($e);
    }
}
