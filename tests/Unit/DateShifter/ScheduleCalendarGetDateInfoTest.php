<?php

declare(strict_types=1);

namespace Tests\Unit\DateShifter;

use Exception;
use PHPUnit\Framework\TestCase;
use App\Lib\DateShifter\Calendar\ScheduleCalendar;
use DateTime;

class ScheduleCalendarGetDateInfoTest extends TestCase
{
    private ScheduleCalendar $scheduleCalendar;


    public function setUp(): void
    {
        $this->scheduleCalendar = ScheduleCalendar::createFromStrings(
            [
                '23.07.2021',
                '24.07.2021',
            ],
            [
                '01.08.2021'
            ]
        );
    }


    /**
     * @dataProvider getDateInfoProvider
     * @throws Exception
     */
    public function testGetDateInfo(string $date, bool $isHoliday, bool $isWorking): void
    {
        $dateInfo = $this->scheduleCalendar->getDateInfo(new DateTime($date));

        $this->assertEquals($isHoliday, $dateInfo->isHoliday());
        $this->assertEquals($isWorking, $dateInfo->isWorking());
    }


    public function getDateInfoProvider(): array
    {
        return [
            ['22.07.2021', false, true],
            ['23.07.2021', true, false],
            ['24.07.2021', true, false],
            ['25.07.2021', false, false],
            ['26.07.2021', false, true],

            ['30.07.2021', false, true],
            ['31.07.2021', false, false],
            ['01.08.2021', false, true],
            ['02.08.2021', false, true],

            // Случайные даты из 2020
            ['06.03.2020', false, true],
            ['07.03.2020', false, false],
            ['08.03.2020', false, false],
            ['09.03.2020', false, true],

            // Случайные даты из 2022
            ['30.09.2022', false, true],
            ['01.10.2022', false, false],
            ['02.10.2022', false, false],
            ['03.10.2022', false, true]
        ];
    }
}
