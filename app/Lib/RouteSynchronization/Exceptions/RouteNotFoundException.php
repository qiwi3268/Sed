<?php

declare(strict_types=1);

namespace App\Lib\RouteSynchronization\Exceptions;


/**
 * Запрашиваемый маршрут не найден
 */
final class RouteNotFoundException extends RouteSynchronizationException
{
    public function __construct(string $name)
    {
        parent::__construct("Маршрут: '$name' не найден");
    }
}
