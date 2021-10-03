<?php

declare(strict_types=1);

namespace App\Lib\RouteSynchronization\Exceptions;


/**
 * Отсутствует заполнитель маршрута
 */
final class MissingPlaceholderException extends RouteSynchronizationException
{
    public function __construct(string $placeholder)
    {
        parent::__construct("Заполнитель маршрута: '$placeholder' отсутствует");
    }
}
