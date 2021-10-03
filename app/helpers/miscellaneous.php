<?php

declare(strict_types=1);


/**
 * Обёртка для работы с функцией preg_match
 *
 * Возвращает требуемые вхождения в виде массива/строки или null
 *
 * @param string $pattern строка без именованных групп
 * @param string[]|string|null $matches
 * <pre>
 * array   если групп больше одной. Если * группа не найдена - значение элемента будет false
 * string  если группа одна. Или если групп нет, то полное вхождение шаблона
 * null    если вхождений не найдено
 * </pre>
 * @return bool true, если было любое вхождение (matches не null)
 * @throws InvalidArgumentException
 */
function pm(string $pattern, string $subject, array|string|null &$matches = null): bool
{
    // Константа PREG_UNMATCHED_AS_NULL необходима из-за некорректной работы
    // стандартной функции при отсутствии вхождения групп
    if (@preg_match($pattern, $subject, $m, PREG_UNMATCHED_AS_NULL) === false) {
        $msg  = preg_last_error_msg();
        $code = preg_last_error();
        throw new InvalidArgumentException("Ошибка в работе функции preg_match. Message: '$msg'. Code: $code");
    }

    foreach ($m as &$item) $item ??= false;

    $matchesCount = count($m);

    $matches = match ($matchesCount) {
        0       => null,
        1       => $m[0], // Полное вхождение шаблона
        2       => $m[1], // Первая и единственная группа
        default => []
    };

    if ($matches === []) {

        for ($f = 1; $f < $matchesCount; $f++) {
            $matches[] = $m[$f];
        }
    }
    return !is_null($matches);
}


/**
 * Разрешает дату
 *
 * @throws InvalidArgumentException
 */
function resolve_date(null|string|DateTimeInterface $date = null): DateTimeImmutable
{
    if (is_null($date)) {
        return new DateTimeImmutable();
    } elseif (is_string($date)) {

        try {
            return new DateTimeImmutable($date);
        } catch (Exception) {
            throw new InvalidArgumentException("Ошибка при создании объекта даты из строки: '$date'");
        }

    } else {
        return DateTimeImmutable::createFromInterface($date);
    }
}
