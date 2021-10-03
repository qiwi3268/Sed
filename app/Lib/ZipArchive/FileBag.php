<?php

declare(strict_types=1);

namespace App\Lib\ZipArchive;

use Webmozart\Assert\Assert;


final class FileBag
{
    private array $names = [];
    private array $files = [];



    /**
     * Добавляет файл в сумку
     *
     * Обеспечивает уникальные наименования файлов
     *
     * @param string $path абсолютный путь к файлу
     */
    public function add(string $path, ?string $name = null)
    {
        Assert::fileExists($path);
        Assert::notEmpty($name ??= basename($path));

        if (array_key_exists($name, $this->names)) {
            $name = '(' . $this->names[$name]++ . ') ' . $name;
        } else {
            $this->names[$name] = 1;
        }

        $this->files[] = [$path, $name];
    }


    /**
     * @return array
     * <pre>
     * [
     *     [path, name],
     *     [path, name],
     *     ...
     * ]
     * </pre>
     */
    public function getFiles(): array
    {
        return $this->files;
    }


    public function isEmpty(): bool
    {
        return empty($this->files);
    }
}
