<?php

declare(strict_types=1);

namespace App\Lib\Filesystem\PrivateStorage;

use App\Lib\Filesystem\PrivateStorage\Exceptions\PrivateStorageIteratorException;
use App\Lib\Filesystem\PrivateStorage\Exceptions\StarPathException;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Iterator;
use Generator;


/**
 * Рекурсивный итератор по хранилищу
 */
final class PrivateStorageIterator
{
    private string $path;


    /**
     * @param string|null $subDir поддиректория относительно точки входа в хранилище.
     * Если null, то итератор будет проходить по всему хранилищу
     * @throws PrivateStorageIteratorException
     */
    public function __construct(?string $subDir = null)
    {
        $adapter = PrivateDisk::getAdapter();

        if (is_string($subDir)) {

            if (!pm('/^\d{4}(\/\d{2})?$/', $subDir)) {
                throw new PrivateStorageIteratorException("Некорректная поддиректория: '$subDir'");
            }
            if (!$adapter->exists($subDir)) {
                throw new PrivateStorageIteratorException("В хранилище отсутствует поддиректория: '$subDir'");
            }
        } else {
            $subDir = '';
        }
        $this->path = $adapter->path($subDir);
    }


    /**
     * Создаёт итератор, который рекурсивно обходит все файлы в хранилище
     *
     * Ключ - абсолютный путь к файлу
     * Значение - объект SplFileInfo
     */
    public function createIterator(): Iterator
    {
        $directoryIterator = new RecursiveDirectoryIterator(
            $this->path,
            RecursiveDirectoryIterator::CURRENT_AS_FILEINFO
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
            | RecursiveDirectoryIterator::SKIP_DOTS
        );
        return new RecursiveIteratorIterator($directoryIterator);
    }


    /**
     * Создаёт генератор, который возвращает объекты StarPath
     *
     * @throws PrivateStorageIteratorException
     */
    public function createStarPathGenerator(): Generator
    {
        foreach ($this->createIterator() as $absolutePath => $unused)
        {
            try {
                yield StarPath::createFromAbsolutePath($absolutePath);
            } catch (StarPathException $e) {
                throw new PrivateStorageIteratorException("Ошибка при создании объекта StarPath: '{$e->getMessage()}'");
            }
        }
    }
}
