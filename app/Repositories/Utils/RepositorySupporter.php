<?php

declare(strict_types=1);

namespace App\Repositories\Utils;


final class RepositorySupporter
{

    /**
     * Создаёт строку заполнителей
     *
     * @param string $format формат, в который будет обёрнут знак вопроса.
     * Должен содержать только %s спецификатор
     * @return string '?, ?, ?'
     */
    public function createPlaceholders(array $values, string $format = '%s'): string
    {
        return implode(
            ', ',
            array_fill(0, count($values), sprintf($format, '?'))
        );
    }
}
