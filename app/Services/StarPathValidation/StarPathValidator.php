<?php

declare(strict_types=1);

namespace App\Services\StarPathValidation;

use App\Lib\Filesystem\PrivateStorage\Exceptions\PrivateStorageManagerException;
use App\Services\StarPathValidation\Exceptions\StarPathValidationException;

use App\Models\Files\File;
use App\Lib\Filesystem\PrivateStorage\StarPath;


final class StarPathValidator
{

    public function __construct(private string $starPath)
    {}


    /**
     * Проверка синтаксиса
     *
     * @throws StarPathValidationException
     */
    public function validateSyntax(): self
    {
        if (!StarPath::validate($this->starPath)) {
            throw new StarPathValidationException("Некорректные данные для формирования starPath строки: '$this->starPath'");
        }
        return $this;
    }


    /**
     * Проверка ФС
     *
     * Предполагается, что ранее был вызван метод проверки validateSyntax
     *
     * @throws StarPathValidationException
     */
    public function validateFilesystem(): self
    {
        $starPath = new StarPath($this->starPath);

        try {
            // Проверка указанной поддиректории
            $storage = $starPath->getPrivateStorageManager();
        } catch (PrivateStorageManagerException $e) {
            throw StarPathValidationException::fromPrevious($e);
        }

        $hashName = $starPath->getHashName();

        if (!$storage->has($hashName)) {
            $path = $starPath->getAbsolutePath();
            throw new StarPathValidationException("Файл: '$path' физически отсутствует в ФС сервера");
        }
        return $this;
    }


    /**
     * Проверка БД
     *
     * Предполагается, что ранее был вызван метод проверки validateSyntax
     *
     * @throws StarPathValidationException
     */
    public function validateDatabase(): self
    {
        if (!File::whereStarPath($this->starPath)->exists()) {
            throw new StarPathValidationException("Запись файла со starPath: '$this->starPath' отсутствует в БД");
        }
        return $this;
    }
}
