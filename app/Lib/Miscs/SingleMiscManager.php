<?php

declare(strict_types=1);

namespace App\Lib\Miscs;

use App\Lib\Miscs\Exceptions\MiscClassDoesNotExistException;


interface SingleMiscManager
{
    public function existsByAlias(string $alias): bool;

    /**
     * @throws MiscClassDoesNotExistException
     */
    public function getClassNameByAlias(string $alias): string;
}
