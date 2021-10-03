<?php

declare(strict_types=1);

namespace App\Services\Pagination;

use Webmozart\Assert\Assert;


final class PaginationUtils
{

    /**
     * Приводит порядковый номер и количество элементов на странице к sql limit и offset
     *
     * @param int $currentPage порядковый номер страницы
     * @param int $perPage количество элементов на странице
     * @return array
     * <pre>
     * [
     *     0 => int, // limit
     *     1 => int  // offset
     * ]
     * </pre>
     */
    public function pagePerPageToLimitOffset(int $currentPage, int $perPage): array
    {
        Assert::allGreaterThan([$currentPage, $perPage], 0);

        return [
            $perPage,
            ($currentPage - 1) * $perPage
        ];
    }


    /**
     * Возвращает последнюю возможную страницу
     */
    public function getLastPage(int $total, int $perPage): int
    {
        Assert::allGreaterThan([$total, $perPage], 0);
        return (int) ceil($total / $perPage);
    }
}
