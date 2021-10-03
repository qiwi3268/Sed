<?php

declare(strict_types=1);

namespace Tests\Unit\DateShifter;

use Exception;
use PHPUnit\Framework\TestCase;
use App\Lib\DateShifter\DateShifter;
use App\Lib\DateShifter\DateCalculator;
use DateTimeImmutable;

class DateCalculatorTest extends TestCase
{
    private DateCalculator $dateCalculator;


    public function setUp(): void
    {
        $this->dateCalculator = new DateCalculator(
            new FakeScheduleCalendar(),
            new DateShifter(new FakeScheduleCalendar())
        );
    }


    /**
     * @dataProvider isWorkingProvider
     * @throws Exception
     */
    public function testIsWorking(string $date, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            $this->dateCalculator->isWorking(new DateTimeImmutable($date))
        );
    }


    public function isWorkingProvider(): array
    {
        return [
            ['29.07.2021', true],
            ['30.07.2021', true],
            ['31.07.2021', false],
            ['01.08.2021', false],
            ['02.08.2021', false],
            ['03.08.2021', true],
            ['04.08.2021', true],
            ['05.08.2021', false],
            ['06.08.2021', false],
            ['07.08.2021', false],
            ['08.08.2021', false],
            ['09.08.2021', true],
            ['15.08.2021', true],
            ['19.08.2021', false],
            ['21.08.2021', true],
            ['22.08.2021', true],
        ];
    }


    /**
     * @dataProvider isNotWorkingAndHolidayProvider
     * @throws Exception
     */
    public function testIsNotWorkingAndHoliday(string $date, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            $this->dateCalculator->isNotWorkingAndHoliday(new DateTimeImmutable($date))
        );
    }


    public function isNotWorkingAndHolidayProvider(): array
    {
        return [
            ['29.07.2021', false],
            ['30.07.2021', false],
            ['31.07.2021', false],
            ['01.08.2021', false],
            ['02.08.2021', true],
            ['03.08.2021', false],
            ['04.08.2021', false],
            ['05.08.2021', true],
            ['06.08.2021', true],
            ['07.08.2021', false],
            ['08.08.2021', false],
            ['09.08.2021', false],
            ['15.08.2021', false],
            ['19.08.2021', true],
            ['21.08.2021', false],
            ['22.08.2021', false],
        ];
    }


    /**
     * @dataProvider isNotWorkingAndNotHolidayProvider
     * @throws Exception
     */
    public function testIsNotWorkingAndNotHoliday(string $date, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            $this->dateCalculator->isNotWorkingAndNotHoliday(new DateTimeImmutable($date))
        );
    }


    public function isNotWorkingAndNotHolidayProvider(): array
    {
        return [
            ['29.07.2021', false],
            ['30.07.2021', false],
            ['31.07.2021', true],
            ['01.08.2021', true],
            ['02.08.2021', false],
            ['03.08.2021', false],
            ['04.08.2021', false],
            ['05.08.2021', false],
            ['06.08.2021', false],
            ['07.08.2021', true],
            ['08.08.2021', true],
            ['09.08.2021', false],
            ['15.08.2021', false],
            ['19.08.2021', false],
            ['21.08.2021', false],
            ['22.08.2021', false],
            ['13.11.2021', false],
            ['14.11.2021', true],
        ];
    }


    /**
     * @dataProvider getNextWorkingDateProvider
     * @throws Exception
     */
    public function testGetNextWorkingDate(string $date, bool $includeStartDate, string $expectedDate): void
    {
        $this->assertEquals(
            $expectedDate,
            $this->dateCalculator
                ->getNextWorkingDate(new DateTimeImmutable($date), $includeStartDate)
                ->format('d.m.Y')
        );
    }



    public function getNextWorkingDateProvider(): array
    {
        return [
            ['30.07.2021', true,  '30.07.2021'],
            ['30.07.2021', false, '03.08.2021'],
            ['03.08.2021', true,  '03.08.2021'],
            ['03.08.2021', false, '04.08.2021'],
            ['04.08.2021', false, '09.08.2021'],
            ['05.08.2021', true,  '09.08.2021'],
            ['05.08.2021', false, '09.08.2021'],
            ['06.08.2021', true,  '09.08.2021'],
            ['06.08.2021', false, '09.08.2021'],
            ['07.08.2021', true,  '09.08.2021'],
            ['07.08.2021', false, '09.08.2021'],
            ['08.08.2021', true,  '09.08.2021'],
            ['08.08.2021', false, '09.08.2021'],
            ['09.08.2021', true,  '09.08.2021'],
            ['09.08.2021', false, '10.08.2021'],
            ['13.08.2021', false, '15.08.2021'],
            ['18.08.2021', true,  '18.08.2021'],
            ['18.08.2021', false, '20.08.2021'],
            ['20.08.2021', false, '21.08.2021'],
            ['21.08.2021', true,  '21.08.2021'],
            ['21.08.2021', false, '22.08.2021'],
            ['22.08.2021', true,  '22.08.2021'],
            ['22.08.2021', false, '23.08.2021'],
            ['23.08.2021', true,  '23.08.2021'],
            ['23.08.2021', false, '24.08.2021'],
        ];
    }


    /**
     * @dataProvider getPreviousWorkingDateProvider
     * @throws Exception
     */
    public function testGetPreviousWorkingDate(string $date, bool $includeStartDate, string $expectedDate): void
    {
        $this->assertEquals(
            $expectedDate,
            $this->dateCalculator
                ->getPreviousWorkingDate(new DateTimeImmutable($date), $includeStartDate)
                ->format('d.m.Y')
        );
    }


    public function getPreviousWorkingDateProvider(): array
    {
        return [
            ['30.07.2021', true,  '30.07.2021'],
            ['30.07.2021', false, '29.07.2021'],
            ['03.08.2021', true,  '03.08.2021'],
            ['03.08.2021', false, '30.07.2021'],
            ['04.08.2021', false, '03.08.2021'],
            ['05.08.2021', true,  '04.08.2021'],
            ['05.08.2021', false, '04.08.2021'],
            ['06.08.2021', true,  '04.08.2021'],
            ['06.08.2021', false, '04.08.2021'],
            ['07.08.2021', true,  '04.08.2021'],
            ['07.08.2021', false, '04.08.2021'],
            ['08.08.2021', true,  '04.08.2021'],
            ['08.08.2021', false, '04.08.2021'],
            ['09.08.2021', true,  '09.08.2021'],
            ['09.08.2021', false, '04.08.2021'],
            ['13.08.2021', false, '12.08.2021'],
            ['18.08.2021', true,  '18.08.2021'],
            ['18.08.2021', false, '17.08.2021'],
            ['20.08.2021', false, '18.08.2021'],
            ['21.08.2021', true,  '21.08.2021'],
            ['21.08.2021', false, '20.08.2021'],
            ['22.08.2021', true,  '22.08.2021'],
            ['22.08.2021', false, '21.08.2021'],
            ['23.08.2021', true,  '23.08.2021'],
            ['23.08.2021', false, '22.08.2021'],
        ];
    }


    /**
     * @dataProvider getFirstWorkingDateOfMonthProvider
     * @throws Exception
     */
    public function testGetFirstWorkingDateOfMonth(string $date, string $expectedDate): void
    {
        $this->assertEquals(
            $expectedDate,
            $this->dateCalculator
                ->getFirstWorkingDateOfMonth(new DateTimeImmutable($date))
                ->format('d.m.Y')
        );
    }


    public function getFirstWorkingDateOfMonthProvider(): array
    {
        return [
            ['01.08.2021', '03.08.2021'],
            ['02.08.2021', '03.08.2021'],
            ['03.08.2021', '03.08.2021'],
            ['04.08.2021', '03.08.2021'],
            ['14.08.2021', '03.08.2021'],
            ['21.08.2021', '03.08.2021'],
            ['30.08.2021', '03.08.2021'],
            ['31.08.2021', '03.08.2021'],
            ['01.09.2021', '01.09.2021'],
            ['02.09.2021', '01.09.2021'],
            ['20.09.2021', '01.09.2021'],
            ['30.09.2021', '01.09.2021'],
            ['01.01.2022', '03.01.2022'],
            ['02.01.2022', '03.01.2022'],
            ['03.01.2022', '03.01.2022'],
            ['04.01.2022', '03.01.2022'],
            ['20.01.2022', '03.01.2022'],
            ['31.01.2022', '03.01.2022'],
        ];
    }


    /**
     * @dataProvider getLastWorkingDateOfMonthProvider
     * @throws Exception
     */
    public function testGetLastWorkingDateOfMonth(string $date, string $expectedDate): void
    {
        $this->assertEquals(
            $expectedDate,
            $this->dateCalculator
                ->getLastWorkingDateOfMonth(new DateTimeImmutable($date))
                ->format('d.m.Y')
        );
    }


    public function getLastWorkingDateOfMonthProvider(): array
    {
        return [
            ['01.08.2021', '31.08.2021'],
            ['02.08.2021', '31.08.2021'],
            ['03.08.2021', '31.08.2021'],
            ['04.08.2021', '31.08.2021'],
            ['14.08.2021', '31.08.2021'],
            ['21.08.2021', '31.08.2021'],
            ['30.08.2021', '31.08.2021'],
            ['31.08.2021', '31.08.2021'],
            ['01.09.2021', '30.09.2021'],
            ['02.09.2021', '30.09.2021'],
            ['20.09.2021', '30.09.2021'],
            ['30.09.2021', '30.09.2021'],
            ['01.10.2021', '29.10.2021'],
            ['02.10.2021', '29.10.2021'],
            ['03.10.2021', '29.10.2021'],
            ['04.10.2021', '29.10.2021'],
            ['20.10.2021', '29.10.2021'],
            ['31.10.2021', '29.10.2021'],
        ];
    }


    /**
     * @dataProvider isFirstWorkingDateOfMonthProvider
     * @throws Exception
     */
    public function testIsFirstWorkingDateOfMonth(string $date, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            $this->dateCalculator->isFirstWorkingDateOfMonth(new DateTimeImmutable($date))
        );
    }


    public function isFirstWorkingDateOfMonthProvider(): array
    {
        return [
            ['01.08.2021', false],
            ['02.08.2021', false],
            ['03.08.2021', true],
            ['04.08.2021', false],
            ['20.08.2021', false],
            ['31.08.2021', false],
            ['01.09.2021', true],
            ['02.09.2021', false],
            ['20.09.2021', false],
            ['30.09.2021', false],
            ['01.01.2022', false],
            ['02.01.2022', false],
            ['03.01.2022', true],
            ['04.01.2022', false],
            ['20.01.2022', false],
            ['31.01.2022', false],
        ];
    }


    /**
     * @dataProvider isLastWorkingDateOfMonthProvider
     * @throws Exception
     */
    public function testIsLastWorkingDateOfMonth(string $date, bool $expectedResult): void
    {
        $this->assertEquals(
            $expectedResult,
            $this->dateCalculator->isLastWorkingDateOfMonth(new DateTimeImmutable($date))
        );
    }


    public function isLastWorkingDateOfMonthProvider(): array
    {
        return [
            ['29.08.2021', false],
            ['30.08.2021', false],
            ['31.08.2021', true],
            ['01.09.2021', false],
            ['02.09.2021', false],
            ['29.09.2021', false],
            ['30.09.2021', true],
            ['27.10.2021', false],
            ['28.10.2021', false],
            ['29.10.2021', true],
            ['30.10.2021', false],
            ['31.10.2021', false],
        ];
    }


    /**
     * @dataProvider getDatesBeforeNextWorkingDateProvider
     * @throws Exception
     */
    public function testGetDatesBeforeNextWorkingDate(
        string $date,
        bool $includeStartDate,
        bool $includeNextWorkingDate,
        array $expectedDates
    ): void {

        $this->assertEquals(
            $expectedDates,
            array_map(
                fn (DateTimeImmutable $d) => $d->format('d.m.Y'),
                $this->dateCalculator->getDatesBeforeNextWorkingDate(
                    new DateTimeImmutable($date),
                    $includeStartDate,
                    $includeNextWorkingDate
                )
            )
        );
    }


    public function getDatesBeforeNextWorkingDateProvider(): array
    {
        return [
            ['29.07.2021', false, false, []],
            ['29.07.2021', false, true, ['30.07.2021']],
            ['29.07.2021', true, false, ['29.07.2021']],
            ['29.07.2021', true, true, ['29.07.2021']],

            ['30.07.2021', false, false, ['31.07.2021', '01.08.2021', '02.08.2021']],
            ['30.07.2021', false, true, ['31.07.2021', '01.08.2021', '02.08.2021', '03.08.2021']],
            ['30.07.2021', true, false, ['30.07.2021']],
            ['30.07.2021', true, true, ['30.07.2021']],

            ['31.07.2021', false, false, ['01.08.2021', '02.08.2021']],
            ['31.07.2021', false, true, ['01.08.2021', '02.08.2021', '03.08.2021']],
            ['31.07.2021', true, false, ['31.07.2021', '01.08.2021', '02.08.2021']],
            ['31.07.2021', true, true, ['31.07.2021', '01.08.2021', '02.08.2021', '03.08.2021']],

            ['04.08.2021', false, false, ['05.08.2021', '06.08.2021', '07.08.2021', '08.08.2021']],
            ['04.08.2021', false, true, ['05.08.2021', '06.08.2021', '07.08.2021', '08.08.2021', '09.08.2021']],
            ['04.08.2021', true, false, ['04.08.2021']],
            ['04.08.2021', true, true, ['04.08.2021']],

            ['05.08.2021', false, false, ['06.08.2021', '07.08.2021', '08.08.2021']],
            ['05.08.2021', false, true, ['06.08.2021', '07.08.2021', '08.08.2021', '09.08.2021']],
            ['05.08.2021', true, false, ['05.08.2021', '06.08.2021', '07.08.2021', '08.08.2021']],
            ['05.08.2021', true, true, ['05.08.2021', '06.08.2021', '07.08.2021', '08.08.2021', '09.08.2021']],

            ['06.08.2021', false, false, ['07.08.2021', '08.08.2021']],
            ['06.08.2021', false, true, ['07.08.2021', '08.08.2021', '09.08.2021']],
            ['06.08.2021', true, false, ['06.08.2021', '07.08.2021', '08.08.2021']],
            ['06.08.2021', true, true, ['06.08.2021', '07.08.2021', '08.08.2021', '09.08.2021']],

            ['07.08.2021', false, false, ['08.08.2021']],
            ['07.08.2021', false, true, ['08.08.2021', '09.08.2021']],
            ['07.08.2021', true, false, ['07.08.2021', '08.08.2021']],
            ['07.08.2021', true, true, ['07.08.2021', '08.08.2021', '09.08.2021']],

            ['08.08.2021', false, false, []],
            ['08.08.2021', false, true, ['09.08.2021']],
            ['08.08.2021', true, false, ['08.08.2021']],
            ['08.08.2021', true, true, ['08.08.2021', '09.08.2021']],

            ['22.08.2021', false, false, []],
            ['22.08.2021', false, true, ['23.08.2021']],
            ['22.08.2021', true, false, ['22.08.2021']],
            ['22.08.2021', true, true, ['22.08.2021']],
        ];
    }
}
