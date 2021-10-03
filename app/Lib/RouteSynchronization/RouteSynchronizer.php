<?php

declare(strict_types=1);

namespace App\Lib\RouteSynchronization;

use App\Lib\RouteSynchronization\Exceptions\RouteNotFoundException;
use App\Lib\RouteSynchronization\Exceptions\MissingPlaceholderException;


interface RouteSynchronizer
{

    /**
     * @throws RouteNotFoundException
     * @throws MissingPlaceholderException
     */
    public function generateAbsoluteUrl(string $name, array $placeholders = []): string;
}
