<?php

declare(strict_types=1);

namespace App\Lib\Filesystem\PrivateStorage;

use App\Lib\Filesystem\PrivateStorage\Exceptions\StarPathException;


final class StarPath
{
    private string $dir;
    private string $hashName;


    /**
     * @throws StarPathException
     */
    public function __construct(private string $starPath)
    {
        if(!pm('/^(\d{4})(\d{2})\*([a-z0-9]{30})$/i', $starPath, $m)) {
            throw new StarPathException("Некорректная starPath строка: '$starPath'");
        }
        $this->dir = "$m[0]/$m[1]";
        $this->hashName = $m[2];
    }


    /**
     * Статический конструктор класса
     */
    public static function create(string $dir, string $hashName): self
    {
        return new self(self::createString($dir, $hashName));
    }


    /**
     * Статический конструктор класса из абсолютно пути
     *
     * @throws StarPathException
     */
    public static function createFromAbsolutePath(string $path): self
    {
        if (!pm('/^.+(\d{4})\/(\d{2})\/([a-z0-9]{30})$/i', $path, $m)) {
            throw new StarPathException("Некорректный абсолютный путь для starPath строки: '$path'");
        }
        return new self("$m[0]$m[1]*$m[2]");
    }


    /**
     * Создает starPath строку из начальных данных
     *
     * @param string $dir поддиректория относительно хранилища. В формате гггг/мм
     */
    public static function createString(string $dir, string $hashName): string
    {
        $numbers = str_replace('/', '', $dir);
        // Создание объекта для проверки корректности starPath строки
        return (string) new self("$numbers*$hashName");
    }


    /**
     * Проверяет starPath строку
     */
    public static function validate(string $starPath): bool
    {
        return pm('/^\d{6}\*[a-z0-9]{30}$/i', $starPath);
    }


    public function getDir(): string
    {
        return $this->dir;
    }


    public function getHashName(): string
    {
        return $this->hashName;
    }


    public function getPrivateStorageManager(): PrivateStorageManager
    {
        return new PrivateStorageManager($this->getDir());
    }


    public function getAbsolutePath(): string
    {
        return $this->getPrivateStorageManager()->getAbsolutePath($this->getHashName());
    }


    /**
     * Возвращает исходную starPath строку
     */
    public function __toString(): string
    {
        return $this->starPath;
    }
}
