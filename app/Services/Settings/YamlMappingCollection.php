<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Lib\FileMapping\Exceptions\MappingDoesNotExistException;

use App\Lib\Settings\SettingsParser;
use App\Lib\FileMapping\MappingCollection;
use App\Lib\FileMapping\MappingData;


final class YamlMappingCollection implements MappingCollection
{
    private array $schema;


    public function __construct(SettingsParser $parser)
    {
        $this->schema = $parser->getSchema();
    }


    public function has(string $mapping): bool
    {
        return arr_some($this->schema, fn (array $unused, string $m): bool => $m == $mapping);
    }


    /**
     * @throws MappingDoesNotExistException
     */
    public function get(string $mapping): MappingData
    {
        if (!$this->has($mapping)) {
            throw new MappingDoesNotExistException($mapping);
        }

        $block = $this->schema[$mapping];

        return new MappingData(
            $mapping,
            $block['rule'],
            $block['uploader'],
            $block['entity'],
        );
    }
}
