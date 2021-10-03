<?php

declare(strict_types=1);

namespace App\Repositories\Exceptions;


/**
 * Неэффективный UPDATE / DELETE запрос
 */
final class IneffectiveQueryRepositoryException extends RepositoryException
{

    /**
     * @throws self
     */
    public static function ineffectiveDeletion(): void
    {
        throw new self('Запрос к БД не удалил ни одной строки');
    }

    /**
     * @throws self
     */
    public static function ineffectiveUpdate(): void
    {
        throw new self('Запрос к БД не обновил ни одной строки');
    }
}
