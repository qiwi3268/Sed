<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use App\Lib\ApiBody\SuccessBody;
use App\Lib\ApiBody\ErrorBody;


/**
 * Возвращает json ответ успешного выполнения
 */
function success_response(
    string $message = 'ok',
    mixed $data = null,
    int $status = 200,
    array $headers = [],
): JsonResponse {

    return new JsonResponse(
        (new SuccessBody($message, $data))->getBody(),
        $status,
        $headers
    );
}


/**
 * Возвращает json ответ ошибки
 */
function error_response(
    string $message = '',
    array $errors = [],
    string $code = '',
    int $status = 422, // Unprocessable Entity
    array $headers = [],
): JsonResponse {

    return new JsonResponse(
        (new ErrorBody($message, $errors, $code))->getBody(),
        $status,
        $headers
    );
}
