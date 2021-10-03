<?php

declare(strict_types=1);

namespace App\Lib\Singles;


/**
 * Сравнивает строки
 *
 * Результат сравнения в первую очередь отдаётся "приоритетным" строкам
 */
final class PriorityStringComparator
{
    /**
     * @param string[] $priority
     */
    public function __construct(private array $priority)
    {}


    public function ascCompare(string $a, string $b): int
    {
        if ($a == $b) {
            return 0;
        }

        foreach ($this->priority as $order) {

            if ($a == $order) {
                return -1;
            } elseif ($b == $order) {
                return 1;
            }
        }
        return $a <=> $b;
    }
}
