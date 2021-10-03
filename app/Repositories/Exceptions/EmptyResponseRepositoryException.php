<?php

declare(strict_types=1);

namespace App\Repositories\Exceptions;


final class EmptyResponseRepositoryException extends RepositoryException
{
    public function __construct(string $table, ?string $condition = null)
    {
        $message = is_null($condition)
            ? "Запрос к таблице: '$table' вернул пустой результат"
            : "В таблице: '$table' отсутствует запись по условию: '$condition'";

        parent::__construct($message);
    }
}
