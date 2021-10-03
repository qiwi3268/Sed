<?php

declare(strict_types=1);

namespace App\Services\Files\FileToPngConverting\Exceptions;

use RuntimeException;


final class FileFormatDetectorException extends RuntimeException
{
    public static function unknownFormat(string $format, string $file): void
    {
        throw new self("Неизвестный формат: '$format' файла: '$file'", 1);
    }
}
