<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use App\Lib\Csp\Exceptions\CspException;
use App\Lib\Csp\Exceptions\UnexpectedMessageException;
use WeStacks\TeleBot\Exception\TeleBotException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];


    /**
     * Переопределение родительского метода
     *
     * Родительский класс содержит свойство $internalDontReport, в котором указано ValidationException.
     * Поскольку SPA выполняет валидацию, то не пройденная валидация на сервере является ошибкой приложения.
     */
    protected function shouldntReport(Throwable $e): bool
    {
        if ($e instanceof ValidationException) {
            return false;
        } else {
            return parent::shouldntReport($e);
        }
    }


    public function register(): void
    {
        // Неиспользуемый входной параметр необходим фреймворку для определения класса исключения

        $this->reportable(function (ValidationException $e): void {
            Log::channel('validation_error')->error(
                $e->getMessage(),
                $e->errors()
            );
        })->stop();


        $this->reportable(function (CspException $e): void {
            $context = $e instanceof UnexpectedMessageException
                ? [$e->getUnexpectedMessage()]
                : [];
            Log::channel('csp')->error($e, $context);
        })->stop();

        $this->renderable(function (CspException $e): JsonResponse {
            return error_response('Техническая ошибка', status: 500);
        });


        $this->reportable(function (TeleBotException $e): void {
            Log::channel('telegram')->error($e);
        })->stop();
    }
}
