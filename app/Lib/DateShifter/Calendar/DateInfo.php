<?php

declare(strict_types=1);

namespace App\Lib\DateShifter\Calendar;

use App\Lib\DateShifter\Exceptions\CalendarLogicException;

/**
 * Представляет собой информацию об отдельно взятом дне:
 *
 *                 Рабочий?
 *                /        \
 *              Да          Нет
 *           Праздник?    Праздник?
 *              |          /    \
 *             Нет       Да      Нет
 *
 * Объект позволяет понять, нерабочий день - праздничный или нет
 *
 * Логика работы никак не связана с субботой и воскресеньем, т.к.
 * рабочее расписание не всегда 5/2
 */
final class DateInfo
{
    private bool $isHoliday;
    private bool $isWorking;


    /**
     * @param bool $isHoliday праздничный день?
     * @param bool $isWorking рабочий день?
     *
     * @throws CalendarLogicException
     */
    public function __construct(bool $isHoliday, bool $isWorking)
    {
        if ($isHoliday && $isWorking) {
            throw new CalendarLogicException('Рабочий день не может быть праздничным');
        }
        $this->isHoliday = $isHoliday;
        $this->isWorking = $isWorking;
    }


    /**
     * Является ли день праздничным
     */
    public function isHoliday(): bool
    {
        return $this->isHoliday;
    }


    /**
     * Является ли день рабочим
     */
    public function isWorking(): bool
    {
        return $this->isWorking;
    }
}
