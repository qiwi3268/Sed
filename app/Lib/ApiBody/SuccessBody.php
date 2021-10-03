<?php

declare(strict_types=1);

namespace App\Lib\ApiBody;


final class SuccessBody implements ApiBody
{

    public function __construct(
        private string $message = 'ok',
        private mixed $data = null,
    ) {}


    public function getBody(): array
    {
        return [
            'message' => $this->message,
            'data'    => $this->data,
        ];
    }
}
