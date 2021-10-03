<?php

declare(strict_types=1);

namespace App\Services\Settings\Parsers;

use Symfony\Component\Yaml\Yaml;
use App\Lib\Settings\SettingsParser;


/**
 * Предназначен для работы с yml файлами
 */
final class YamlSimpleSettingsParser implements SettingsParser
{

    public function __construct(private string $path)
    {}


    public function getSchema(): array
    {
        return Yaml::parseFile($this->path);
    }
}
