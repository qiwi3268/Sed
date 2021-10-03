<?php

declare(strict_types=1);

namespace Tests\Unit\DateShifter;

use PHPUnit\Framework\TestCase;
use App\Lib\DateShifter\Calendar\DateInfo;

class DateInfoTest extends TestCase
{
    public function testInvalidInput(): void
    {
        $this->expectExceptionMessage('Рабочий день не может быть праздничным');

        new DateInfo(true, true);
    }


    /**
     * @dataProvider gettersProvider
     */
    public function testGetters(bool $isHoliday, bool $isWorking): void
    {
        $dateInfo = new DateInfo($isHoliday, $isWorking);

        $this->assertEquals($isHoliday, $dateInfo->isHoliday());
        $this->assertEquals($isWorking, $dateInfo->isWorking());
    }


    public function gettersProvider(): array
    {
        return [
            [true,  false, null],
            [false, true,  null],
            [false, false, null],
        ];
    }
}
