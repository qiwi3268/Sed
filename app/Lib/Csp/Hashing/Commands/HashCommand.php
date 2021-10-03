<?php

declare(strict_types=1);

namespace App\Lib\Csp\Hashing\Commands;

use App\Lib\Csp\AlgorithmsList;
use App\Lib\Csp\Command;
use Webmozart\Assert\Assert;


/**
 * Команда создания хэш файла
 */
final class HashCommand
{

    /**
     * @param string $hashDir абсолютный путь, по которому будет сохранен результирующий хэш файл
     * @param string $hashAlg алгоритм хэширования
     * @param string $file абсолютный путь к исходному файлу
     */
    public function __construct(
        private string $hashDir,
        private string $hashAlg,
        private string $file
    ) {
        Assert::directory($hashDir);
        Assert::true(AlgorithmsList::existHashAlgorithm($hashAlg));
        Assert::fileExists($file);
    }


    public function getCommand(): array
    {
        return [
            Command::CPROCSP,
            '-hash',
            '-dir',
            $this->hashDir,
            '-provtype',    // тип криптопровайдера
            '80',
            '-hashalg',     // алгоритм хэширования
            $this->hashAlg,
            '-hex',         // сохранить хэш файла в виде шестнадцатеричной строки
            $this->file
        ];
    }


    public function getHashDir(): string
    {
        return $this->hashDir;
    }


    public function getFile(): string
    {
       return $this->file;
    }
}
