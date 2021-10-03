<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Lib\DatabaseMagicNumbers\Exceptions\ContainerDoesNotExistException;

use App\Lib\Settings\SettingsParser;
use App\Lib\DatabaseMagicNumbers\DatabaseMagicNumbersManager;
use App\Lib\DatabaseMagicNumbers\MagicNumbersContainer;


final class YamlDatabaseMagicNumbersManager implements DatabaseMagicNumbersManager
{
    private array $schema;


    public function __construct(SettingsParser $parser)
    {
        $this->schema = $parser->getSchema();
    }


    /**
     * @throws ContainerDoesNotExistException
     */
    public function getContainer(string $name): MagicNumbersContainer
    {
        if (!array_key_exists($name, $this->schema)) {
            throw new ContainerDoesNotExistException($name);
        }
        return new MagicNumbersContainer($name, $this->schema[$name]);
    }
}
