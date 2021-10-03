<?php

declare(strict_types=1);

namespace App\Providers\Utils;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Cache\Repository as CacheRepository;
use App\Services\Settings\Parsers\YamlCachingSettingsParser;
use App\Services\Settings\Parsers\YamlSimpleSettingsParser;


final class ProviderHelper
{
    /**
     * @param string $path относительный путь от директории settings. Начинается без '/'
     * @throws BindingResolutionException
     */
    public static function createYamlCachingSettingsParser(string $path): YamlCachingSettingsParser
    {
        return new YamlCachingSettingsParser(
            app()->make(CacheRepository::class),
            now()->addDays(7),
            base_path("settings/$path")
        );
    }


    /**
     * @param string $path относительный путь от директории settings. Начинается без '/'
     */
    public static function createYamlSimpleSettingsParser(string $path): YamlSimpleSettingsParser
    {
        return new YamlSimpleSettingsParser(base_path("settings/$path"));
    }
}
