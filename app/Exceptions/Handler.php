<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
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

    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            // Если это исключение валидации, верните JSON-ответ с сообщениями об ошибках
            return response()->json([
                'errors' => $e->validator->errors(),
            ], 422); // Можете выбрать другой HTTP-код, например, 422
        }

        // Обработка других типов исключений

        return parent::render($request, $e);
    }


}
