<?php

declare(strict_types=1);

namespace App\Repositories\Conferences\Navigation;

use DateTimeInterface;


interface ConferenceNavigationRepository
{
    public function getTodaysCount(int $userId): int;
    public function getTodays(int $userId): array;

    public function getPlannedCount(int $userId): int;
    public function getPlanned(int $userId): array;

    /**
     * @return string[]
     */
    public function getDatesWithConferencesByYear(int $year): array;

    public function getByDate(DateTimeInterface $date): array;
}
