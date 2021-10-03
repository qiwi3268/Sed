<?php

declare(strict_types=1);

namespace App\Services\Files\Uploading\Uploaders;

use App\Services\Files\FileWrapper;


interface FileUploader
{

    /**
     * Возвращает правила валидации входных данных
     */
    public function getValidationRules(): ?array;


    /**
     * Обработка файла при сохранении в БД
     *
     * @param array $requestInput данные запроса
     */
    public function processingToDatabase(FileWrapper $fileWrapper, array $requestInput): FileWrapper;


    /**
     * Обработка файла при извлечении из БД
     */
    public function processingFromDatabase(FileWrapper $fileWrapper): FileWrapper;
}
