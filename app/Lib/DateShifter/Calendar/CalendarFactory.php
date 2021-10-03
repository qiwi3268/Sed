<?php

declare(strict_types=1);

namespace App\Lib\DateShifter\Calendar;

use App\Lib\DateShifter\Exceptions\CalendarCreationException;

/**
 * Описывает фабрику для создания объекта {@see Calendar}
 */
interface CalendarFactory
{
    /**
     * @throws CalendarCreationException в случае ошибки при создании календаря
     */
    public function create(): Calendar;
}
