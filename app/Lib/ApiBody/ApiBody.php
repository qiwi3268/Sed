<?php

declare(strict_types=1);

namespace App\Lib\ApiBody;


interface ApiBody
{

    /**
     * Возвращает тело ответа в виде ассоциативного массива
     */
    public function getBody(): array;
}
