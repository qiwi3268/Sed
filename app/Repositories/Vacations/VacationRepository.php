<?php

declare(strict_types=1);

namespace App\Repositories\Vacations;

use DateTimeInterface;


interface VacationRepository
{
    public function getForNext30Days(): array;

    public function getByYearAndMonth(int $year, int $month): array;

    public function getNext(): array;

    public function getPast(): array;

    /**
     * @return int[]
     */
    public function getUsersOnVacationIdsByDate(DateTimeInterface $date): array;

    public function getUsersOnVacationCountByDate(DateTimeInterface $date): int;

    public function getUsersOnVacationByDate(DateTimeInterface $date): array;

    /**
     * @param DateTimeInterface[] $dates
     */
    public function getUsersOnVacationByStartDates(array $dates): array;
}
