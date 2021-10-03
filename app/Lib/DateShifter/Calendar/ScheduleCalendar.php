<?php

declare(strict_types=1);

namespace App\Lib\DateShifter\Calendar;

use Exception;
use RuntimeException;
use App\Lib\DateShifter\Exceptions\CalendarLogicException;
use DateTime;
use DateTimeInterface;

/**
 * Производственный календарь, который автоматически определяет:
 *
 * С понедельника по пятницу - рабочие
 * Суббота и воскресенье - нерабочие
 *
 * В конструкторе класса есть возможность указать:
 *     1. Праздничные дни
 *        Это может быть любой день недели.
 *        Важно указывать праздничные дни, которые пришлись на сб или вс
 *     2. Рабочие дни
 *        Это может быть только сб или вс.
 *        Например, если в вашей стране какая-либо суббота официально
 *        объявлена рабочим днём
 */
final class ScheduleCalendar implements Calendar
{
    public const DATE_FORMAT = 'd.m.Y';

    private array $holidays = [];
    private array $workdays = [];


    /**
     * @param DateTimeInterface[] $holidays праздничные дни
     * @param DateTimeInterface[] $workdays рабочие дни (сб или вс)
     * @throws CalendarLogicException
     */
    public function __construct(array $holidays, array $workdays)
    {
        $uniquenessCheck = [];

        foreach ($holidays as $holiday) {
            $i = $this->toInternalDate($holiday);
            $this->holidays[] = $i;
            $uniquenessCheck[$i] = true;
        }

        foreach ($workdays as $workday) {
            $i = $this->toInternalDate($workday);
            // Дата сохраняется только в том случае, если это сб или вс, т.к.
            // дни недели с пн по пт автоматически считаются рабочими
            if ($this->isSatOrSun($workday)) {
                $this->workdays[] = $i;
            }
            $uniquenessCheck[$i] = true;
        }

        if (count($uniquenessCheck) != count($holidays) + count($workdays)) {
            throw new CalendarLogicException('Во входных параметрах присутствуют повторяющиеся даты');
        }
    }


    /**
     * Вспомогательный конструктор для работы со строковыми массивами
     *
     * @param string[] $holidays праздничные дни
     * @param string[] $workdays рабочие дни
     * @throws CalendarLogicException
     */
    public static function createFromStrings(array $holidays, array $workdays): self
    {
        try {
            $holidays = array_map(fn (string $h): DateTime => new DateTime($h), $holidays);
            $workdays = array_map(fn (string $w): DateTime => new DateTime($w), $workdays);
        } catch (Exception $e) {
            throw new CalendarLogicException('Ошибка при создании объекта даты', 0, $e);
        }
        return new self($holidays, $workdays);
    }


    public function getDateInfo(DateTimeInterface $date): DateInfo
    {
        $internalDate = $this->toInternalDate($date);

        if (in_array($internalDate, $this->holidays)) {

            $isHoliday = true;
            $isWorking = false;

        } elseif (in_array($internalDate, $this->workdays)) {

            $isHoliday = false;
            $isWorking = true;

        } elseif ($this->isSatOrSun($date)) {

            $isHoliday = false;
            $isWorking = false;

        } else { // пн-пт

            $isHoliday = false;
            $isWorking = true;

        }
        return new DateInfo($isHoliday, $isWorking);
    }


    /**
     * Возвращает сокращенную дату
     *
     * Убираются точки и ведущий ноль у месяца. Пример: 01.02.2020 до 0122020
     *
     * Чтобы не возникало неоднозначностей - ведущий ноль нельзя убирать одновременно у дня и месяца
     *
     * Предположим, что имеется 12 месяцев и каждый месяц содержит 30 дней, тогда:
     *  - экономия на днях:    9 * 12 = 108 символов
     *  - экономия на месяцах: 30 * 9 = 270 символов
     */
    private function serializeDate(string $internalDate): string
    {
        return DateTime::createFromFormat(self::DATE_FORMAT, $internalDate)->format('dnY');
    }


    /**
     * Выполняет обратную операцию по отношению к {@see serializeDate}
     *
     * @throws RuntimeException
     */
    private function unserializeDate(string $shortDate): string
    {
        if (!preg_match('/^(\d{2})(\d{1,2})(\d{4})$/', $shortDate, $m)) {
            throw new RuntimeException("Ошибка при разборе сериализованной даты: '$shortDate'");
        }
        // Здесь подразумевается, что константа DATE_FORMAT = 'd.m.Y'
        return sprintf('%s.%02s.%s', $m[1], $m[2], $m[3]);
    }


    /**
     * Приводит дату к формату, который используется для внутреннего хранения
     */
    private function toInternalDate(DateTimeInterface $date): string
    {
        return $date->format(self::DATE_FORMAT);
    }


    /**
     * Является ли день субботой или воскресеньем
     */
    private function isSatOrSun(DateTimeInterface $date): bool
    {
        return in_array($date->format('N'), [6, 7]);
    }


    public function __serialize(): array
    {
        // Данные будут храниться в одном массиве с bool флагом
        $data = [];

        foreach ($this->holidays as $holiday) {
            $data[$this->serializeDate($holiday)] = true;
        }

        foreach ($this->workdays as $workday) {
            $data[$this->serializeDate($workday)] = false;
        }

        return $data;
    }


    public function __unserialize(array $data): void
    {
        foreach ($data as $shortDate => $isHoliday) {

            $date = $this->unserializeDate((string) $shortDate);

            if ($isHoliday) {
                $this->holidays[] = $date;
            } else {
                $this->workdays[] = $date;
            }
        }
    }
}
