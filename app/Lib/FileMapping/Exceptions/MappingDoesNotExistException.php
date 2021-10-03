<?php

declare(strict_types=1);

namespace App\Lib\FileMapping\Exceptions;

use LogicException;


final class MappingDoesNotExistException extends LogicException
{
    public function __construct(string $mapping)
    {
        parent::__construct("Маппинг: '$mapping' отсутствует в коллекции");
    }
}
