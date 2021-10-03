<?php

declare(strict_types=1);

namespace App\Lib\DateShifter;

use App\Lib\DateShifter\Calendar\Calendar;

use DateInterval;
use DatePeriod;
use DateTimeInterface;
use DateTimeImmutable as DTI;

/**
 * Выполняет вычисления, связанные со сдвигом дат и производственным календарём
 */
final class DateCalculator
{
    private Calendar $calendar;
    private DateShifter $dateShifter;


    public function __construct(Calendar $calendar, DateShifter $dateShifter)
    {
        $this->calendar = $calendar;
        $this->dateShifter = $dateShifter;
    }


    /**
     * Является ли день рабочим
     */
    public function isWorking(DateTimeInterface $date): bool
    {
        return $this->calendar->getDateInfo($date)->isWorking();
    }


    /**
     * Является ли день нерабочим праздничным
     */
    public function isNotWorkingAndHoliday(DateTimeInterface $date): bool
    {
        $dateInfo = $this->calendar->getDateInfo($date);

        return !$dateInfo->isWorking() && $dateInfo->isHoliday();
    }


    /**
     * Является ли день нерабочим непраздничным
     */
    public function isNotWorkingAndNotHoliday(DateTimeInterface $date): bool
    {
        $dateInfo = $this->calendar->getDateInfo($date);

        return !$dateInfo->isWorking() && !$dateInfo->isHoliday();
    }


    /**
     * Возвращает ближайший рабочий день
     *
     * @param bool $includeStartDate
     * true  - день рассчитывается начиная с входной даты
     * false - день рассчитывается со следующего календарного дня
     */
    public function getNextWorkingDate(DateTimeInterface $date, bool $includeStartDate = false): DTI
    {
        if (!$includeStartDate) {
            $date = $this->dateShifter->shiftOnCalendarDays($date, 1);
        }

        return $this->isWorking($date)
            ? DTI::createFromInterface($date)
            : $this->dateShifter->shiftOnWorkdays($date, 1);
    }


    /**
     * Возвращает предыдущий рабочий день
     *
     * @param bool $includeStartDate
     * true  - день рассчитывается начиная с входной даты
     * false - день рассчитывается с предыдущего календарного дня
     */
    public function getPreviousWorkingDate(DateTimeInterface $date, bool $includeStartDate = false): DTI
    {
        if (!$includeStartDate) {
            $date = $this->dateShifter->shiftOnCalendarDays($date, -1);
        }

        return $this->isWorking($date)
            ? DTI::createFromInterface($date)
            : $this->dateShifter->shiftOnWorkdays($date, -1);
    }


    /**
     * Возвращает массив дат до ближайшего рабочего дня
     *
     * @param bool $includeStartDate требуется ли включать входную дату в результирующий массив.
     * Ближайший рабочий день при этом рассчитывается с этой даты
     * @param bool $includeNextWorkingDate требуется ли включать полученный ближайший рабочий день
     * в результирующий массив
     * @return DTI[]
     */
    public function getDatesBeforeNextWorkingDate(
        DateTimeInterface $date,
        bool $includeStartDate = false,
        bool $includeNextWorkingDate = true
    ): array {

        $date = DTI::createFromInterface($date);

        $workingDate = $this->getNextWorkingDate($date, $includeStartDate);

        if (
            $includeStartDate
            && $date->format('Y-m-d') == $workingDate->format('Y-m-d')
        ) {
            return [$workingDate];
        }

        if ($includeNextWorkingDate) {
            // Т.к. DatePeriod не включает конечную дату
            $workingDate = $workingDate->modify('+1 day');
        }

        $period = new DatePeriod(
            $date,
            new DateInterval('P1D'),
            $workingDate,
            $includeStartDate ? 0 : DatePeriod::EXCLUDE_START_DATE
        );

        $result = [];

        // Получение результата из итератора
        foreach ($period as $d) {
            $result[] = $d;
        }

        return $result;
    }


    /**
     * Возвращает первый рабочий день месяца
     */
    public function getFirstWorkingDateOfMonth(DateTimeInterface $date): DTI
    {
        return $this->getNextWorkingDate(
            DTI::createFromInterface($date)->modify('first day of this month'),
            true
        );
    }


    /**
     * Является ли день первым рабочим днём месяца
     */
    public function isFirstWorkingDateOfMonth(DateTimeInterface $date): bool
    {
        return $date->format('d') == $this->getFirstWorkingDateOfMonth($date)->format('d');
    }


    /**
     * Возвращает последний рабочий день месяца
     */
    public function getLastWorkingDateOfMonth(DateTimeInterface $date): DTI
    {
        return $this->getPreviousWorkingDate(
            DTI::createFromInterface($date)->modify('last day of this month'),
            true
        );
    }


    /**
     * Является ли день последним рабочим днём месяца
     */
    public function isLastWorkingDateOfMonth(DateTimeInterface $date): bool
    {
        return $date->format('d') == $this->getLastWorkingDateOfMonth($date)->format('d');
    }
}
