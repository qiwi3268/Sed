<?php

declare(strict_types=1);

namespace App\Lib\FileMapping;

use App\Lib\FileMapping\Exceptions\MappingDoesNotExistException;


interface MappingCollection
{

    public function has(string $mapping): bool;

    /**
     * @throws MappingDoesNotExistException
     */
    public function get(string $mapping): MappingData;
}
