<?php

declare(strict_types=1);

namespace App\Lib\Singles;


final class Inflector
{

    /**
     * Принимает падежи слов:
     *
     * @param string $case1 яблоко, рубль,  день
     * @param string $case2 яблока, рубля,  дня
     * @param string $case3 яблок,  рублей, дней
     */
    public function __construct(
        private string $case1,
        private string $case2,
        private string $case3
    ) {}


    public function inflect(int $number, bool $includeNumber = true): string
    {
        $abs = abs($number) % 100;

        if ($abs > 19) {
            $abs %= 10;
        }

        $word = match ($abs) {
            1       => $this->case1,
            2, 3, 4 => $this->case2,
            default => $this->case3
        };

        return $includeNumber ? "$number $word" : $word;
    }
}
