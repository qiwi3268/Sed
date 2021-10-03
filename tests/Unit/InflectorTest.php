<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Lib\Singles\Inflector;


class InflectorTest extends TestCase
{

    /**
     * @dataProvider okProvider
     */
    public function testOk(int $num, string $expected, bool $includeNumber = false): void
    {
        $inflector = new Inflector('рубль', 'рубля', 'рублей');

        $this->assertEquals($expected, $inflector->inflect($num, $includeNumber));
    }


    public function okProvider(): array
    {
        return [
            [0, 'рублей'],
            [1, 'рубль'],
            [2, 'рубля'],
            [3, 'рубля'],
            [4, 'рубля'],
            [5, 'рублей'],
            [10, 'рублей'],
            [15, 'рублей'],
            [19, 'рублей'],
            [20, 'рублей'],
            [21, 'рубль'],
            [25, 'рублей'],
            [31, 'рубль'],
            [51, 'рубль'],
            [71, 'рубль'],
            [111, 'рублей'],
            [-101, 'рубль'],
            [-102, 'рубля'],
            [-103, 'рубля'],
            [-104, 'рубля'],
            [-105, 'рублей'],
            [-1, '-1 рубль', true],
            [106, '106 рублей', true],
        ];
    }
}
