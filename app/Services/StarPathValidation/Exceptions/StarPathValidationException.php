<?php

declare(strict_types=1);

namespace App\Services\StarPathValidation\Exceptions;

use Exception;
use InvalidArgumentException;


final class StarPathValidationException extends InvalidArgumentException
{

    public static function fromPrevious(Exception $e): self
    {
        return new self($e->getMessage(), $e->getCode());
    }
}
