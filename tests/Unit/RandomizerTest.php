<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Lib\Singles\Randomizer;

class RandomizerTest extends TestCase
{

    public function testOk(): void
    {
        $items = ['a', 'b', 'c', 'd', 'e', 'f'];

        $randomizer = new Randomizer($items);

        $result = [];

        for ($i = 0; $i < 18; $i++) {
            $result[] = $randomizer->get();
        }

        $this->assertEquals([
            'a' => 3,
            'b' => 3,
            'c' => 3,
            'd' => 3,
            'e' => 3,
            'f' => 3,
        ], array_count_values($result));

        $this->assertNotEquals(
            [...$items, ...$items, ...$items],
            $result
        );
    }


    public function testOneItem(): void
    {
        $randomizer = new Randomizer(['a']);

        $this->assertEquals(
            ['a', 'a'],
            [$randomizer->get(), $randomizer->get()]
        );
    }


    public function testConstructException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Randomizer([]);
    }
}
