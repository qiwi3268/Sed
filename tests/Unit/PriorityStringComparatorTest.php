<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Lib\Singles\PriorityStringComparator;



class PriorityStringComparatorTest extends TestCase
{
    private PriorityStringComparator $comparator;


    public function setUp(): void
    {
        $this->comparator = new PriorityStringComparator([
            'Исаев',
            'Грищенко',
            'Громов'
        ]);
    }


    /**
     * @dataProvider ascSortingProvider
     */
    public function testAscSorting(array $forSort, array $expected): void
    {
        usort($forSort, [$this->comparator, 'ascCompare']);

        $this->assertEquals($expected, $forSort);
    }


    public function ascSortingProvider(): array
    {
        return [
            [
                ['Исаев', 'Громов', 'Грищенко'],
                ['Исаев', 'Грищенко', 'Громов']
            ],
            [
                ['Антошкин', 'Слугин', 'Макаров'],
                ['Антошкин', 'Макаров', 'Слугин']
            ],
            [
                ['Исаев', 'Громов', 'Грищенко', 'Макаров', 'Слугин', 'Антошкин'],
                [ 'Исаев', 'Грищенко', 'Громов', 'Антошкин', 'Макаров', 'Слугин']
            ],
            [
                ['Митусов', 'Кулаев', 'Сафина', 'Сабельников', 'Исаев', 'Антошкин', 'Аксенова', 'Громов'],
                [ 'Исаев', 'Громов', 'Аксенова', 'Антошкин', 'Кулаев', 'Митусов', 'Сабельников', 'Сафина']
            ],
        ];
    }
}
