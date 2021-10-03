<?php

declare(strict_types=1);

use Webmozart\Assert\Assert;



/**
 * Вернул ли callback true хоть для одного элемента массива
 *
 * @param callable $callback принимает значение и ключ массива, возвращает bool
 */
function arr_some(array $array, callable $callback): bool
{
    foreach ($array as $key => $value) {

        if ($callback($value, $key) === true) {
            return true;
        }
    }
    return false;
}


/**
 * Возвращает первый элемент массива, для которого callback вернул true
 *
 * @param callable $callback принимает значение и ключ массива, возвращает bool
 * @param bool $shortReturn возвращать только значение, или [ключ, значение]
 * @throws InvalidArgumentException исключение обязательно, поскольку иначе
 * невозможно отличить отсутствие вхождения от значения null/false
 */
function arr_first(array $array, callable $callback, bool $shortReturn = true): mixed
{
    foreach ($array as $key => $value) {

        if ($callback($value, $key) === true) {

            return $shortReturn
                ? $value
                : [$key, $value];
        }
    }
    throw new InvalidArgumentException('Не найдено подходящего элемента массива');
}


/**
 * Проверяет существование всех ключей в массиве
 *
 * @param int|string[] $keys
 */
function arr_all_key_exists(array $array, array $keys): bool
{
    foreach ($keys as $key) {

        if (!array_key_exists($key, $array)) {
            return false;
        }
    }
    return true;
}


/**
 * Объединяет элементы ассоциативного массива в строку
 *
 * @param string $keySeparator разделитель ключа и значения
 * @param string $partsSeparator разделитель между частями
 */
function assoc_implode(array $array, string $keySeparator = '=', string $partsSeparator = ', '): string
{
    $result = [];

    foreach ($array as $key => $value) {

        $result[] = "$key$keySeparator$value";
    }
    return implode($partsSeparator, $result);
}


/**
 * Разделяет входной массив на ассоциативный на основе callback функций
 *
 * Ключ нового массива - ключ callback функции.
 * Значение - массив элементов из входного массива, для которых callback вернул true
 *
 * @param array $callbacks ассоциативный массив callback функций
 * @throws LogicException
 */
function arr_split(array $array, array $callbacks): array
{
    Assert::isMap($callbacks);

    $result = [];

    foreach ($callbacks as $section => $unused) {
        $result[$section] = [];
    }

    foreach ($array as $key => $value) {

        foreach ($callbacks as $section => $callback) {

            if ($callback($value, $key) === true) {
                $result[$section][] = $value;
                continue 2;
            }
        }
        throw new LogicException("Элемент массива по ключу: '$key' не подошёл ни под одну callback функцию");
    }
    return $result;
}


/**
 * Аналог стандартной функции {@see array_map()}
 *
 * Отличия:
 * - работает не только с массивом
 * - в callback функцию передаётся значение вместе с ключом
 */
function arr_map(callable $callback, iterable $array): array
{
    $result = [];

    foreach ($array as $key => $value) {
        $result[] = $callback($value, $key);
    }

    return $result;
}


/**
 * Все ли элементы массива null
 */
function arr_all_is_null(array $array): bool
{
    foreach ($array as $value) {

        if (!is_null($value)) {

            return false;
        }
    }
    return true;
}


/**
 * Приводит все элементы на первом уровне вложенности к int типу
 */
function arr_to_int(array $array): array
{
    $result = [];

    foreach ($array as $key => $value) {
        $result[$key] = (int) $value;
    }

    return $result;
}
