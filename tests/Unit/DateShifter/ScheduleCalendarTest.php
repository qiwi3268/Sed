<?php

declare(strict_types=1);

namespace Tests\Unit\DateShifter;

use Exception;
use App\Lib\DateShifter\Exceptions\CalendarLogicException;
use PHPUnit\Framework\TestCase;
use App\Lib\DateShifter\Calendar\ScheduleCalendar;
use DateTime;

class ScheduleCalendarTest extends TestCase
{
    public function testInvalidHolidayStringDate(): void
    {
        $this->expectException(CalendarLogicException::class);

        ScheduleCalendar::createFromStrings(['abc'], []);
    }


    public function testInvalidWorkdayStringDate(): void
    {
        $this->expectException(CalendarLogicException::class);

        ScheduleCalendar::createFromStrings([], ['abc']);
    }


    /**
     * @throws Exception
     */
    public function testCreatingFromStrings(): void
    {
        $scheduleCalendar = ScheduleCalendar::createFromStrings(
            [
                $d1 = '23.08.2021',
                $d2 = '25.08.2021'
            ],
            [
                $d3 = '28.08.2021'
            ]
        );

        $d1 = new DateTime($d1);
        $d2 = new DateTime($d2);
        $d3 = new DateTime($d3);

        $this->assertTrue($scheduleCalendar->getDateInfo($d1)->isHoliday());
        $this->assertFalse($scheduleCalendar->getDateInfo($d1)->isWorking());

        $this->assertTrue($scheduleCalendar->getDateInfo($d2)->isHoliday());
        $this->assertFalse($scheduleCalendar->getDateInfo($d2)->isWorking());

        $this->assertFalse($scheduleCalendar->getDateInfo($d3)->isHoliday());
        $this->assertTrue($scheduleCalendar->getDateInfo($d3)->isWorking());
    }


    /**
     * @dataProvider notUniqueDatesProvider
     */
    public function testNotUniqueDates(array $holidays, array $workdays): void
    {
        $this->expectExceptionMessage('Во входных параметрах присутствуют повторяющиеся даты');

        ScheduleCalendar::createFromStrings($holidays, $workdays);
    }


    public function notUniqueDatesProvider(): array
    {
        return [
            [
                ['02.01.2021', '02.01.2021'], []
            ],
            [
                [], ['02.01.2021', '02.01.2021']
            ],
            [
                ['02.01.2021'], ['02.01.2021']
            ],
        ];
    }


    public function testSerialize(): void
    {
        $scheduleCalendar = ScheduleCalendar::createFromStrings(
            ['23.08.2021', '25.08.2021', '27.10.2020', '28.10.2020'],
            ['28.08.2021', '29.08.2021', '24.12.2022', '25.12.2021']
        );
        $this->assertEquals($scheduleCalendar, unserialize(serialize($scheduleCalendar)));
    }
}
