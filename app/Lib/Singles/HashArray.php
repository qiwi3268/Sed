<?php

declare(strict_types=1);

namespace App\Lib\Singles;

use InvalidArgumentException;
use Traversable;


/**
 * Позволяет максимально быстро проверять наличие элементов
 */
final class HashArray
{

    /**
     * Ассоциативный bool массив
     *
     * @var bool[]
     */
    private array $data = [];


    /**
     * @param string[] $array
     */
    public function __construct(array $array = [])
    {
        foreach ($array as $element) {
            $this->add($element);
        }
    }


    /**
     * Статический конструктор класса
     */
    public static function createByCallback(Traversable|array $elements, callable $callback): self
    {
        $array = [];

        foreach ($elements as $key => $value) {

            $array[] = $callback($value, $key);
        }
        return new self($array);
    }


    /**
     * Добавляет элемент в хэш массив
     *
     * @throws InvalidArgumentException
     */
    public function add(string $element): self
    {
        if ($element === '') {
            throw new InvalidArgumentException('Добавляемый элемент не может быть пустой строкой');
        }
        if (is_numeric($element)) {
            throw new InvalidArgumentException("Добавляемый элемент: '$element' не может быть числовым значением");
        }
        if ($this->has($element)) {
            throw new InvalidArgumentException("Элемент: '$element' уже добавлен в хэш массив");
        }

        $this->data[$element] = true;
        return $this;
    }


    /**
     * Присутствует ли элемент в хэш массиве
     */
    public function has(string $element): bool
    {
        return array_key_exists($element, $this->data);
    }


    /**
     * Отсутствует ли элемент в хэш массиве
     */
    public function missing(string $element): bool
    {
        return !$this->has($element);
    }


    /**
     * Возвращает массив с добавленными элементами
     *
     * @return string[]
     */
    public function getElements(): array
    {
        return array_keys($this->data);
    }
}
