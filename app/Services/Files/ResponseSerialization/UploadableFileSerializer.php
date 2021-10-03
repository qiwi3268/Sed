<?php

declare(strict_types=1);

namespace App\Services\Files\ResponseSerialization;

use App\Services\Files\FileWrapper;
use JsonSerializable;


/**
 * Загружаемый файл
 */
final class UploadableFileSerializer implements JsonSerializable
{
    private array $serialized;


    public function __construct(FileWrapper $fileWrapper)
    {
        $file = $fileWrapper->getFileModel();

        $this->serialized = array_merge([
            'starPath'      => $file->getRequiredAttribute('star_path'),
            'originalName'  => $file->getRequiredAttribute('original_name'),
            'size'          => $file->getRequiredAttribute('size'),
        ], $fileWrapper->getFields());
    }


    public function jsonSerialize(): array
    {
        return $this->serialized;
    }
}
