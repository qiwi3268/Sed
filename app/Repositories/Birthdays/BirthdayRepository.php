<?php

declare(strict_types=1);

namespace App\Repositories\Birthdays;

use DateTimeInterface;


interface BirthdayRepository
{
    public function getAll(): array;

    /**
     * @param DateTimeInterface[] $dates даты, у которых будет учтён только порядковый номер дня и месяца
     */
    public function getByDatesOfBirth(array $dates): array;
}
