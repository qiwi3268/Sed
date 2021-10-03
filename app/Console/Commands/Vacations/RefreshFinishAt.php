<?php

declare(strict_types=1);

namespace App\Console\Commands\Vacations;

use Illuminate\Console\Command;
use App\Models\Vacations\Vacation;
use App\Lib\DateShifter\DateShifter;
use App\Lib\DateShifter\Calendar\CalendarCachingFactory;


final class RefreshFinishAt extends Command
{

    protected $signature = 'vacation:refresh_finish_at';

    protected $description = "Обновляет столбец 'finish_at' в соответствии с новым рабочим расписанием";


    public function handle(CalendarCachingFactory $factory): int
    {
        // DI для DateShifter не используется, т.к. важно не использовать кэш
        // при создании объекта календаря
        $dateShifter = new DateShifter($factory->deleteCache()->create());

        /** @var Vacation $vacation */
        foreach (Vacation::select(['id', 'start_at', 'finish_at', 'duration'])->cursor() as $vacation) {

            ['start_at' => $startAt, 'finish_at' => $finishAt] = $vacation->getAttributes();

            $newFinishAt = $dateShifter->shiftOnCalendarDaysWithHolidays(
                resolve_date($startAt),
                $vacation->duration - 1
            )->format('Y-m-d');

            if ($finishAt != $newFinishAt) {
                $vacation->update(['finish_at' => $newFinishAt]);
                $this->info("Запись с id: $vacation->id: обновлена дата с $finishAt на $newFinishAt");
            }
        }
        $this->info('Команда выполнена успешно');
        return 0;
    }
}
