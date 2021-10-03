<?php

declare(strict_types=1);

namespace App\Lib\ApiBody;


final class ErrorBody implements ApiBody
{

    public function __construct(
        private string $message = '',
        private array $errors = [],
        private string $code = '',
    ) {}


    public function getBody(): array
    {
        return [
            'message' => $this->message,
            'errors'  => $this->errors,
            'code'    => $this->code,
        ];
    }
}
