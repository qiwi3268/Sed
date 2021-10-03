<?php

declare(strict_types=1);

namespace App\Lib\DatabaseMagicNumbers\Exceptions;

use LogicException;


final class ContainerDoesNotExistException extends LogicException
{
    public function __construct(string $name)
    {
        parent::__construct("Контейнер: '$name' не существует");
    }
}
