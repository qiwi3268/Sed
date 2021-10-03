<?php

declare(strict_types=1);

namespace App\Lib\DatabaseMagicNumbers;

use App\Lib\DatabaseMagicNumbers\Exceptions\ContainerDoesNotExistException;


interface DatabaseMagicNumbersManager
{
    /**
     * @throws ContainerDoesNotExistException
     */
    public function getContainer(string $name): MagicNumbersContainer;
}
