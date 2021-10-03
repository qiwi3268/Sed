<?php

declare(strict_types=1);

namespace App\Lib\DateTime;

use InvalidArgumentException;

use DateTimeInterface;
use DateTimeImmutable;


final class DateUtils
{

    /**
     * Проверяет корректность даты
     */
    public static function checkDate(string $date, string $format): bool
    {
        $d = DateTimeImmutable::createFromFormat($format, $date);

        // Объект даты сформировался из строки без ошибки
        // и новая дата в этом же формате является исходной строкой
        // т.к. формирование даты из строки с несуществующими днями создает
        // объект с другим месяцем, годом и т.д.
        return $d !== false && $d->format($format) == $date;
    }


    /**
     * Создаёт объект на основе проверенной даты
     *
     * @throws InvalidArgumentException
     */
    public static function createCheckedDate(string $date, string $format): DateTimeImmutable
    {
        if (!self::checkDate($date, $format)) {
            throw new InvalidArgumentException("Дата: '$date' некорректна");
        }
        return DateTimeImmutable::createFromFormat($format, $date);
    }


    /**
     * Суббота или воскресенье
     */
    public static function isSatOrSun(DateTimeInterface $date): bool
    {
        return in_array((int) $date->format('N'), [6, 7]);
    }

}
