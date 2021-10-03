<?php

declare(strict_types=1);

namespace App\Repositories\Utils;

use LogicException;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;


final class Builder
{
    private array $items;


    /**
     * @param array $items индексный массив с ассоциативными массивами
     */
    public function __construct(array $items)
    {
        $items = $this->normalizeItems($items);

        Assert::isList($items);
        Assert::allIsNonEmptyMap($items);

        $this->items = $items;
    }


    /**
     * Рекурсивное преобразование объектов к массивам
     */
    public function normalizeItems(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {

            if (is_object($value)) {
                $value = (array) $value;
            }

            if (is_array($value)) {
                $value = $this->normalizeItems($value);
            }

            $result[$key] = $value;
        }
        return $result;
    }


    /**
     * Статический конструктор
     */
    public static function create(array $items): self
    {
        return new self($items);
    }


    public function addItems(array $items): self
    {
        return new self([...$this->items, ...$items]);
    }


    /**
     * Перемещает данные по указанному префиксу в подмассив
     */
    public function deepenByPrefix(string $prefix, string $generatedKey): self
    {
        $result = [];

        foreach ($this->items as $item) {

            $i = [];
            $i[$generatedKey] = [];

            foreach ($item as $key => $value) {

                if (str_starts_with($key, $prefix)) {
                    $i[$generatedKey][$key] = $value;
                } else {
                    $i[$key] = $value;
                }
            }
            $result[] = $i;
        }
        return new self($result);
    }


    /**
     * Вставляет зависимые данные из JOIN части внутрь основного элемента
     *
     * @param string $prefix префикс, с которого начинаются зависимые данные
     */
    public function compileJoin(string $id, string $prefix, string $generatedKey, bool $skipNullable = true): self
    {
        $result = [];
        $ids = [];

        foreach ($this->items as $item) {

            $main = [];
            $main[$generatedKey] = [];
            $sub = [];

            foreach ($item as $key => $value) {

                if (str_starts_with($key, $prefix)) {
                    $sub[$key] = $value;
                } else {
                    $main[$key] = $value;
                }
            }

            if (!($skipNullable && arr_all_is_null($sub))) {
                $main[$generatedKey][] = $sub;
            }

            $idValue = $item[$id];

            if (array_key_exists($idValue, $ids)) {
                $result[$ids[$idValue]][$generatedKey][] = $sub;
            } else {
                $ids[$idValue] = count($result);
                $result[$ids[$idValue]] = $main;
            }
        }
        return new self($result);
    }


    /**
     * Вставляет зависимые внешние элементы в соответствующие им главные элементы
     */
    public function insertOuterItems(string $id, string $generatedKey, array $outerItems, string $foreignKey): self
    {
        $result = [];

        foreach ($this->items as $item) {
            $item[$generatedKey] = [];
            $result[] = $item;
        }

        foreach ($this->normalizeItems($outerItems) as $outerItem) {
            $index = $this->searchItem($id, $outerItem[$foreignKey]);
            $result[$index][$generatedKey][] = $outerItem;
        }

        return new self($result);
    }


    /**
     * Рекурсивно удаляет префикс из названия ключей
     */
    public function stripPrefix(string $prefix): self
    {
        return new self(array_map(
            fn (array $item): array => $this->trimmer($item, $prefix), $this->items
        ));
    }


    /**
     * Вспомогательная рекурсивная функция
     */
    private function trimmer(array $array, string $prefix): array
    {
        $result = [];

        foreach ($array as $key => $value) {

            if (str_starts_with((string) $key, $prefix)) {
                $key = Str::after($key, $prefix);
            }

            if (is_array($value)) {
                $value = $this->trimmer($value, $prefix);
            }

            $result[$key] = $value;
        }
        return $result;
    }


    /**
     * Удаляет данные по указанному ключу
     */
    public function deleteKey(string $key): self
    {
        $result = [];

        foreach ($this->items as $item) {
            unset($item[$key]);
            $result[] = $item;
        }

        return new self($result);
    }


    /**
     * Возвращает индексный массив со значениями по указанному ключу
     */
    public function pluck(string $key): array
    {
        return array_map(fn (array $item): mixed => $item[$key], $this->items);
    }


    /**
     * Возвращает индекс первого найденного элемента
     *
     * @throws LogicException
     */
    public function searchItem(string $key, mixed $value): int
    {
        foreach ($this->items as $index => $item) {

            if ($item[$key] === $value) {
                return $index;
            }
        }
        throw new LogicException("По ключу: '$key' не найдено подходящего элемента");
    }


    /**
     * Изменяет элементы
     *
     * @param callable $callback function (array &$item): void
     */
    public function mutateItems(callable $callback): self
    {
        $items = $this->items;

        foreach ($items as &$item) {
            $callback($item);
        }
        unset($item);

        return new self($items);
    }


    /**
     * Возвращает единственный элемент
     *
     * @throws LogicException
     */
    public function singleToArray(): array
    {
        $count = count($this->items);

        if ($count != 1) {
            throw new LogicException("Коллекция содержит: '$count' элемент(ов)");
        }
        return $this->items[0];
    }


    /**
     * Сортирует элементы по возрастанию
     */
    public function sortByKey(string $key): self
    {
        $items = $this->items;
        usort($items, fn (array $i1, array $i2) => $i1[$key] <=> $i2[$key]);
        return new self($items);
    }


    public function isEmpty(): bool
    {
        return empty($this->items);
    }


    public function toArray(): array
    {
        return $this->items;
    }
}
