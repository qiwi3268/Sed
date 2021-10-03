<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\MessageParsing;

use App\Lib\Csp\Exceptions\LogicException;
use ArrayIterator;


final class MessageIterator extends ArrayIterator
{

    public function __construct(array $array)
    {
        // Переиндексация входного массива
        parent::__construct(array_values($array));
    }


    /**
     * Является ли текущий индекс чётным
     */
    public function isEven(): bool
    {
        return $this->key() % 2 == 0;
    }


    /**
     * Следующий элемент
     */
    public function nextItem(): string
    {
        return $this->offsetGet($this->validatedIndex($this->key() + 1));
    }


    /**
     * Последний элемент
     */
    public function lastItem(): string
    {
        return $this->offsetGet($this->validatedIndex($this->count() - 1));
    }


    /**
     * Предпоследний элемент
     */
    public function beforeLastItem(): string
    {
        return $this->offsetGet($this->validatedIndex($this->count() - 2));
    }


    /**
     * Вспомогательный метод для возврата проверенного индекса
     *
     * @throws LogicException
     */
    private function validatedIndex(int $index): int
    {
        if (!$this->offsetExists($index)) {
            throw new LogicException($this, "Индекс: '$index' не существует");
        }
        return $index;
    }
}
