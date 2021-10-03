<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use InvalidArgumentException;


final class Randomizer
{
    private array $items;

    private array $indexes = [];


    /**
     * @throws InvalidArgumentException
     */
    public function __construct(array $items)
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Массив элементов не может быть пустым');
        }
        $this->items = array_values($items);
    }


    public function get(): mixed
    {
        if (empty($this->indexes)) {
            $this->regenerateIndexes();
        }
        return $this->items[array_shift($this->indexes)];
    }


    private function regenerateIndexes(): void
    {
        $range = range(0, count($this->items) - 1);
        shuffle($range);
        $this->indexes = $range;
    }
}
