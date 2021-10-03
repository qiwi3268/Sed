<?php

declare(strict_types=1);

namespace Tests\Unit\DateShifter;

use App\Lib\DateShifter\Calendar\Calendar;
use App\Lib\DateShifter\Calendar\DateInfo;
use App\Lib\DateShifter\Calendar\ScheduleCalendar;
use DateTimeInterface;

class FakeScheduleCalendar implements Calendar
{
    private ScheduleCalendar $scheduleCalendar;


    public function __construct()
    {
        $this->scheduleCalendar = ScheduleCalendar::createFromStrings(
            [
                '02.08.2021', // Пн
                '05.08.2021', // Чт
                '06.08.2021', // Пт
                '19.08.2021', // Чт
                '13.11.2021'  // Сб
            ],
            [
                '15.08.2021', // Вс
                '21.08.2021', // Сб
                '22.08.2021'  // Вс
            ]
        );
    }


    public function getDateInfo(DateTimeInterface $date): DateInfo
    {
        return $this->scheduleCalendar->getDateInfo($date);
    }
}
