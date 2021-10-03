<?php

declare(strict_types=1);

namespace App\Services\Pagination;

use Closure;


/**
 * Обёртка для метода получения элементов из репозитория
 *
 * Добавляет ко входным параметрам limit и offset
 */
final class ItemsMethod
{
    private Closure $method;


    /**
     * @param array $params массив входных параметров метода.
     * Не должен включать limit и offset
     */
    public function __construct(callable $method, array $params)
    {
        $this->method = fn (int $limit, int $offset): array => $method(...$params, ...[$limit, $offset]);
    }


    public function call(int $limit, int $offset): array
    {
        return ($this->method)($limit, $offset);
    }
}
