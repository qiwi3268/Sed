<?php

declare(strict_types=1);

namespace App\Services\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Lib\RouteSynchronization\RouteSynchronizer;


final class LengthAwarePaginatorFactory
{

    /**
     * Принятое в системе количество элементов на странице
     */
    public const PER_PAGE = 25;


    public function __construct(
        private RouteSynchronizer $routeSynchronizer,
        private PaginationUtils $paginationUtils
    ) {}


    /**
     * @param string $routeName название маршрута для синхронизации url
     */
    public function create(
        int $itemsCount,
        ItemsMethod $itemsMethod,
        int $currentPage,
        string $routeName
    ): LengthAwarePaginator {

        // Обработка значения текущей страницы
        if ($currentPage <= 0 || $itemsCount == 0) {
            $currentPage = 1;
        } else {
            $lastPage = $this->paginationUtils->getLastPage($itemsCount, self::PER_PAGE);

            if ($currentPage > $lastPage) {
                $currentPage = $lastPage;
            }
        }

        [$limit, $offset] = $this->paginationUtils->pagePerPageToLimitOffset($currentPage, self::PER_PAGE);

        return new LengthAwarePaginator(
            $itemsMethod->call($limit, $offset),
            $itemsCount,
            self::PER_PAGE,
            $currentPage,
            [
                'path' => $this->routeSynchronizer->generateAbsoluteUrl($routeName)
            ]
        );
    }
}
