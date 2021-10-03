<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\Lib\DateTime\DateUtils;
use DateTimeImmutable;
use DateTime;


class DateUtilsTest extends TestCase
{

    public function testIsSatOrSun(): void
    {
        $d1 = new DateTime('19.07.2021'); // пн
        $d2 = new DateTime('20.07.2021'); // вт
        $d3 = new DateTime('21.07.2021'); // ср
        $d4 = new DateTime('22.07.2021'); // чт
        $d5 = new DateTime('23.07.2021'); // пт
        $d6 = new DateTime('24.07.2021'); // сб
        $d7 = new DateTime('25.07.2021'); // вс

        $this->assertFalse(DateUtils::isSatOrSun($d1));
        $this->assertFalse(DateUtils::isSatOrSun($d2));
        $this->assertFalse(DateUtils::isSatOrSun($d3));
        $this->assertFalse(DateUtils::isSatOrSun($d4));
        $this->assertFalse(DateUtils::isSatOrSun($d5));
        $this->assertTrue(DateUtils::isSatOrSun($d6));
        $this->assertTrue(DateUtils::isSatOrSun($d7));
    }


    public function testCheckDate(): void
    {
        $this->assertTrue(
            DateUtils::checkDate('20.07.2021', 'd.m.Y')
        );

        $this->assertFalse(
            DateUtils::checkDate('20.07.2021', 'd-m-Y')
        );

        $this->assertFalse(
            DateUtils::checkDate('32.07.2021', 'd.m.Y')
        );

        $this->assertFalse(
            DateUtils::checkDate('a', 'd.m.Y')
        );
    }


    public function testCreateCheckedDateException(): void
    {
        $this->expectExceptionMessage("Дата: 'abc' некорректна");
        DateUtils::createCheckedDate('abc', 'd.m.Y');
    }


    public function testCreateCheckedDateOk(): void
    {
        $date = DateUtils::createCheckedDate('20.07.2021', 'd.m.Y');
        $this->assertInstanceOf(DateTimeImmutable::class, $date);
    }
}
