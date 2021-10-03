<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Lib\Settings\SettingsParser;
use App\Lib\DateShifter\Calendar\ScheduleCalendar;
use App\Lib\DateShifter\Calendar\CalendarFactory;


final class YamlScheduleCalendarFactory implements CalendarFactory
{

    public function __construct(private SettingsParser $parser)
    {}


    public function create(): ScheduleCalendar
    {
        $schema = $this->parser->getSchema();

        return ScheduleCalendar::createFromStrings(
            $schema['holidays'] ?? [],
            $schema['workdays'] ?? []
        );
    }
}
