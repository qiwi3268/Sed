<?php

declare(strict_types=1);

namespace Tests\Unit\DateShifter;

use Exception;
use PHPUnit\Framework\TestCase;
use App\Lib\DateShifter\DateShifter;
use DateTimeImmutable;

class DateShifterTest extends TestCase
{
    private DateShifter $dateShifter;


    public function setUp(): void
    {
        $this->dateShifter = new DateShifter(new FakeScheduleCalendar());
    }


    /**
     * @dataProvider shiftOnWorkdaysProvider
     * @throws Exception
     */
    public function testShiftOnWorkdays(string $date, int $offset, string $expectedDate): void
    {
        $this->assertEquals(
            $expectedDate,
            $this->dateShifter
                ->shiftOnWorkdays(new DateTimeImmutable($date), $offset)
                ->format('d.m.Y')
        );
    }


    public function shiftOnWorkdaysProvider(): array
    {
        return [
            ['29.07.2021', 0, '29.07.2021'],
            ['29.07.2021', 2, '03.08.2021'],
            ['03.08.2021', 1, '04.08.2021'],
            ['04.08.2021', 1, '09.08.2021'],
            ['05.08.2021', 1, '09.08.2021'],
            ['06.08.2021', 1, '09.08.2021'],
            ['07.08.2021', 1, '09.08.2021'],
            ['08.08.2021', 1, '09.08.2021'],
            ['10.08.2021', -2, '04.08.2021'],
            ['13.08.2021', 1, '15.08.2021'],
            ['18.08.2021', 2, '21.08.2021'],
            ['21.08.2021', 1, '22.08.2021'],
            ['22.08.2021', 1, '23.08.2021'],
        ];
    }


    /**
     * @dataProvider shiftOnCalendarDaysWithHolidaysProvider
     * @throws Exception
     */
    public function testShiftOnCalendarDaysWithHolidays(string $date, int $offset, string $expectedDate): void
    {
        $this->assertEquals(
            $expectedDate,
            $this->dateShifter
                ->shiftOnCalendarDaysWithHolidays(new DateTimeImmutable($date), $offset)
                ->format('d.m.Y')
        );
    }


    public function shiftOnCalendarDaysWithHolidaysProvider(): array
    {
        return [
            ['29.07.2021', 0, '29.07.2021'],
            ['29.07.2021', 2, '31.07.2021'],
            ['03.08.2021', 1, '04.08.2021'],
            ['04.08.2021', 1, '07.08.2021'],
            ['05.08.2021', 1, '07.08.2021'],
            ['06.08.2021', 1, '07.08.2021'],
            ['07.08.2021', 1, '08.08.2021'],
            ['08.08.2021', 1, '09.08.2021'],
            ['10.08.2021', -4, '04.08.2021'],
            ['13.08.2021', 1, '14.08.2021'],
            ['13.08.2021', 2, '15.08.2021'],
            ['18.08.2021', 2, '21.08.2021'],
            ['21.08.2021', 1, '22.08.2021'],
            ['22.08.2021', 1, '23.08.2021'],
        ];
    }


    /**
     * @dataProvider shiftOnCalendarDaysProvider
     * @throws Exception
     */
    public function testShiftOnCalendarDays(string $date, int $offset, string $expectedDate): void
    {
        $this->assertEquals(
            $expectedDate,
            $this->dateShifter
                ->shiftOnCalendarDays(new DateTimeImmutable($date), $offset)
                ->format('d.m.Y')
        );
    }


    public function shiftOnCalendarDaysProvider(): array
    {
        return [
            ['29.07.2021', 0, '29.07.2021'],
            ['29.07.2021', 8, '06.08.2021'],
            ['06.08.2021', -8, '29.07.2021'],
            ['10.08.2021', 20, '30.08.2021'],
            ['30.08.2021', -20, '10.08.2021'],
        ];
    }
}

