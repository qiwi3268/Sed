<?php

declare(strict_types=1);

namespace App\Services\Files\Uploading\Rules;


/**
 * Вспомогательный класс для хранения шаблонов
 *
 */
final class RulesLibrary
{
    public const MULTIPLE_ALLOWED = true;
    public const MULTIPLE_FORBIDDEN = false;

    /**
     * При указании null будет браться параметр upload_max_filesize из php.ini файла
     */
    public const MAX_FILE_SIZES = [
        null,
    ];

    /**
     * Первая точка не указывается
     */
    public const ALLOWABLE_EXTENSIONS = [
        null,
    ];

    public const FORBIDDEN_SYMBOLS = [
        [',']
    ];
}
