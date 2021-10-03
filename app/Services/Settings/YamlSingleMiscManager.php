<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Lib\Miscs\Exceptions\MiscClassDoesNotExistException;
use App\Lib\Settings\SettingsParser;
use App\Lib\Miscs\SingleMiscManager;


final class YamlSingleMiscManager implements SingleMiscManager
{
    private array $schema;


    public function __construct(SettingsParser $parser)
    {
        $this->schema = $parser->getSchema()['single_miscs'];
    }


    public function existsByAlias(string $alias): bool
    {
        return arr_some($this->schema, fn (array $m) => $m['alias'] == $alias);
    }


    /**
     * @throws MiscClassDoesNotExistException
     */
    public function getClassNameByAlias(string $alias): string
    {
        $className = arr_first($this->schema, fn (array $m) => $m['alias'] == $alias)['class'];

        if (!class_exists($className)) {
            throw new MiscClassDoesNotExistException($alias, $className);
        }
        return $className;
    }
}
