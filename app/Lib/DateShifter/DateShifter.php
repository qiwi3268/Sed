<?php

declare(strict_types=1);

namespace App\Lib\DateShifter;

use App\Lib\DateShifter\Calendar\Calendar;
use App\Lib\DateShifter\Calendar\DateInfo;
use DateTimeInterface;
use DateTimeImmutable as DTI;
use DateInterval;

/**
 * Выполняет сдвиги дат в соответствии с производственным календарём
 */
final class DateShifter
{
    private Calendar $calendar;


    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }


    /**
     * Сдвиг по рабочим дням
     */
    public function shiftOnWorkdays(DateTimeInterface $date, int $offset): DTI
    {
        return $this->shift(
            $date,
            $offset,
            fn (DateInfo $i): bool => $i->isWorking()
        );
    }


    /**
     * Сдвиг по календарным дням с учётом праздничных дней
     */
    public function shiftOnCalendarDaysWithHolidays(DateTimeInterface $date, int $offset): DTI
    {
        return $this->shift(
            $date,
            $offset,
            fn (DateInfo $i): bool => $i->isWorking() || !$i->isHoliday()
        );
    }


    /**
     * Сдвиг по календарным дням
     */
    public function shiftOnCalendarDays(DateTimeInterface $date, int $offset): DTI
    {
        return $this->shift(
            $date,
            $offset,
            fn (DateInfo $i): bool => true
        );
    }


    /**
     * Выполняет операцию по сдвигу дней
     *
     * @param callable $continueCondition условие, по которому засчитывается смещение.
     * function (DateInfo $dateInfo): bool
     */
    private function shift(DateTimeInterface $date, int $offset, callable $continueCondition): DTI
    {
        $date = DTI::createFromInterface($date);

        $method = $offset > 0 ? 'add' : 'sub';
        $offset = abs($offset);
        $oneDay = new DateInterval('P1D');

        while ($offset) {

            /** @var DTI $date */
            $date = $date->{$method}($oneDay);

            if ($continueCondition($this->calendar->getDateInfo($date))) {
                $offset--;
            }
        }
        return $date;
    }
}
