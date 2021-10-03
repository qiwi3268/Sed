<?php

declare(strict_types=1);

namespace App\Lib\Filesystem\PrivateStorage;

use Webmozart\Assert\Assert;
use Generator;


/**
 * Упаковывает строки starPath в массивы нужного размера.
 * Последняя итерация генератора может вернуть массив меньшего размера
 */
final class StarPathPacker
{
    public function __construct(private PrivateStorageIterator $iterator, private int $packSize = 50)
    {
        Assert::greaterThan($packSize, 0);
    }


    /**
     * @return Generator string starPath[]
     */
    public function getGenerator(): Generator
    {
        $pack = [];

        /** @var StarPath $starPath */
        foreach ($this->iterator->createStarPathGenerator() as $starPath) {

            if (count($pack) < $this->packSize) {
                $pack[] = (string) $starPath;
            } else {
                yield $pack;
                $pack = [(string) $starPath];
            }
        }

        if (!empty($pack)) {
            yield $pack;
        }
    }
}
