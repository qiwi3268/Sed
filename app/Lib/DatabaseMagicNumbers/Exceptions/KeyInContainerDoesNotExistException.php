<?php

declare(strict_types=1);

namespace App\Lib\DatabaseMagicNumbers\Exceptions;

use LogicException;


final class KeyInContainerDoesNotExistException extends LogicException
{
    public function __construct(string $name, string $key)
    {
        parent::__construct("Ключ: '$key' отсутствует в контейнере: '$name'");
    }
}
