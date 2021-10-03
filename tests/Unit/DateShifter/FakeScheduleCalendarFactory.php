<?php

declare(strict_types=1);

namespace Tests\Unit\DateShifter;

use App\Lib\DateShifter\Calendar\Calendar;
use App\Lib\DateShifter\Calendar\CalendarFactory;

final class FakeScheduleCalendarFactory implements CalendarFactory
{
    public function create(): Calendar
    {
        return new FakeScheduleCalendar();
    }
}
