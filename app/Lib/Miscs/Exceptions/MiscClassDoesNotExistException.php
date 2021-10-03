<?php

declare(strict_types=1);

namespace App\Lib\Miscs\Exceptions;

use LogicException;


final class MiscClassDoesNotExistException extends LogicException
{
    public function __construct(string $alias, string $className)
    {
        parent::__construct("По алиасу: '$alias' указан несуществующий класс: '$className'");
    }
}
