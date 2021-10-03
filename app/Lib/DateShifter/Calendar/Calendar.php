<?php

declare(strict_types=1);

namespace App\Lib\DateShifter\Calendar;

use DateTimeInterface;

/**
 * Производственный календарь
 *
 * Описывает класс, который способен предоставить объект {@see DateInfo}
 * для запрашиваемой даты
 */
interface Calendar
{
    public function getDateInfo(DateTimeInterface $date): DateInfo;
}
