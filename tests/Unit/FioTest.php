<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Lib\Singles\Fio;


class FioTest extends TestCase
{

    public function testIsValidPart(): void
    {
        $this->assertTrue(Fio::isValidPart('АА'));
        $this->assertTrue(Fio::isValidPart('А'));
        $this->assertTrue(Fio::isValidPart('а'));
        $this->assertTrue(Fio::isValidPart('а-а'));
        $this->assertTrue(Fio::isValidPart('а-а-а'));

        $this->assertFalse(Fio::isValidPart('АА '));
        $this->assertFalse(Fio::isValidPart('а-'));
        $this->assertFalse(Fio::isValidPart('-а'));
        $this->assertFalse(Fio::isValidPart(' а'));
        $this->assertFalse(Fio::isValidPart(''));
    }


    /**
     * @dataProvider tokenizeStringProvider
     */
    public function testTokenizeString($fio, $expectedTokens): void
    {
        $this->assertEquals($expectedTokens, Fio::tokenizeString($fio));
    }


    public function tokenizeStringProvider(): array
    {
        return [
            ['Аа Бб Вв',       ['Аа', 'Бб', 'Вв']],
            ['Аа-а Бб-б Вв-в', ['Аа-а', 'Бб-б', 'Вв-в']],
            ['Аа Бб',          ['Аа', 'Бб', null]],
            ['Аа Бб-вв',       ['Аа', 'Бб-вв', null]],
            ['Аа Бб 2',        false],
            ['Аа Бб Вв Гг',    false],
            ['Аа 2',           false],
        ];
    }


    public function testConstructException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Fio('Аа', 'Бб', 'В2');
    }


    public function testGetters(): void
    {
        $fio1 = new Fio('ААА', 'Ббб', 'ввв');
        $this->assertEquals('Ааа', $fio1->getLastName());
        $this->assertEquals('Ббб', $fio1->getFirstName());
        $this->assertEquals('Ввв', $fio1->getMiddleName());

        $fio2 = new Fio('ААА', 'Ббб', null);
        $this->assertEquals(null, $fio2->getMiddleName());
    }


    public function testGetLongFio(): void
    {
        $fio1 = new Fio('ААА', 'Ббб', 'ввв');
        $this->assertEquals('Ааа Ббб Ввв', $fio1->getLongFio());

        $fio2 = new Fio('ААА', 'Ббб', null);
        $this->assertEquals('Ааа Ббб', $fio2->getLongFio());

        $fio2 = new Fio('ААА', 'Ббб-б', null);
        $this->assertEquals('Ааа Ббб-б', $fio2->getLongFio());
    }


    public function testGetShortFio(): void
    {
        $fio1 = new Fio('ААА', 'Ббб', 'ввв');
        $this->assertEquals('Ааа Б.В.', $fio1->getShortFio());

        $fio2 = new Fio('ААА', 'Ббб', null);
        $this->assertEquals('Ааа Б.', $fio2->getShortFio());

        $fio2 = new Fio('ААА', 'Ббб-б', null);
        $this->assertEquals('Ааа Б.', $fio2->getShortFio());
    }
}
