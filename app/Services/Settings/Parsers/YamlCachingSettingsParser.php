<?php

declare(strict_types=1);

namespace App\Services\Settings\Parsers;

use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use App\Lib\Settings\SettingsParser;
use App\Lib\Cache\KeyNaming;
use DateTimeInterface;


/**
 * Предназначен для работы с yml файлами
 *
 * Выполняет кэширование полученных настроек
 */
final class YamlCachingSettingsParser implements SettingsParser
{

    public const TAG = 'parsed_yaml_files';

    /**
     * Ключ в кэш хранилище
     */
    private string $key;


    public function __construct(
        private CacheRepository $repository,
        private DateTimeInterface $ttl,
        private string $path
    ) {
        $this->key = KeyNaming::createForCaller([
            Str::after($path, base_path('settings/'))
        ]);
    }


    public function getSchema(): array
    {
        return $this->repository->tags([self::TAG])->remember(
            $this->key,
            $this->ttl,
            fn (): array => Yaml::parseFile($this->path)
        );
    }
}
