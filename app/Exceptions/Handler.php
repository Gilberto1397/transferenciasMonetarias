<?php

namespace App\Exceptions;

use App\Helpers\CreateLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        Throwable::class
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        $this->renderable(function (ValidationException $exception, $request) {
            return response()->json([
                'messages' => $exception->errors(),
                'error' => true,
            ], 406);
        });

        $this->renderable(function (Throwable $exception) {
            CreateLog::logError($exception->getMessage(), $exception->getFile(), $exception->getLine());

            return response()->json([
                'message' => 'Ooops, parece que houve um erro ao tentar criar a conta.',
                'error' => true,
            ], 500);
        });
    }
}
