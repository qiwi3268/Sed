<?php

declare(strict_types=1);

namespace App\Lib\Filesystem\PrivateStorage;

use App\Lib\Filesystem\PrivateStorage\Exceptions\PrivateStorageManagerException;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;


/**
 * Ответственен за конкретную поддиректорию в хранилище
 */
final class PrivateStorageManager
{
    private FilesystemAdapter $adapter;
    private string $dir;


    /**
     * @param string|null $dir поддиректория относительно file system disk
     */
    public function __construct(?string $dir = null)
    {
        $this->adapter = PrivateDisk::getAdapter();
        $this->dir = $this->resolveDir($dir);
    }


    /**
     * Создает поддиректорию, если она ещё не создана.
     * В противном случае проверяет ранее созданную поддиректорию
     *
     * @throws PrivateStorageManagerException
     */
    private function resolveDir(?string $dir = null): string
    {
        if (is_null($dir)) { // Создание поддиректории

            $dir = resolve_date()->format('Y/m');

            if ($this->adapter->missing($dir) && !$this->adapter->createDir($dir)) {
                throw new PrivateStorageManagerException("Ошибка при создании поддиректории: '$dir'");
            }
        } else { // Проверка ранее созданной поддиректории

            if (!pm('/^\d{4}\/\d{2}$/', $dir)) {
                throw new PrivateStorageManagerException("Поддиректория: '$dir' имеет неверный формат");
            }
            if ($this->adapter->missing($dir)) {
                throw new PrivateStorageManagerException("Поддиректория: '$dir' не существует");
            }
        }
        return $dir;
    }


    /**
     * Возвращает свободное (отсутствующее в поддиректории) хэш имя файла
     */
    public function getFreeHashName(): string
    {
        do {
            $hash = Str::random(30);

            if (!$this->has($hash)) {
                return $hash;
            }
        } while (true);
    }


    public function getAbsolutePath(string $hashName): string
    {
        return $this->adapter->path("$this->dir/$hashName");
    }


    /**
     * Проверяет существование файла
     */
    public function has(string $hashName): bool
    {
        return $this->adapter->has("$this->dir/$hashName");
    }


    /**
     * Сохраняет файл со свободным хэш именем
     *
     * @return array [поддиректория относительно хранилища, хэш имя файла]
     * @throws PrivateStorageManagerException
     */
    public function putFileAsFreeHashName(File|UploadedFile $file): array
    {
        $hashName = $this->getFreeHashName();

        $result = $this->adapter->putFileAs($this->dir, $file, $hashName);

        if ($result === false) {
            throw new PrivateStorageManagerException("Ошибка при сохранении файла в поддиректорию: '$this->dir'");
        }
        return [$this->dir, $hashName];
    }
}
