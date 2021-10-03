<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Lib\DateShifter\DateCalculator;
use App\Jobs\Conferences\ProcessConferencesBeforeStarting;


class Kernel extends ConsoleKernel
{

    /**
     * Ручная регистрация команд
     */
    protected $commands = [

    ];


    protected function schedule(Schedule $schedule): void
    {
        $todayIsWorking = app(DateCalculator::class)->isWorking(now());

        // --------------------------------------------------------------------------

        // Обновление прогноза погоды нужно запускать как можно ближе к тому моменту,
        // когда понадобятся данные
        $schedule->command('weather_forecast:refresh_for_two_days')
            ->dailyAt('08:00');

        $schedule->command('telegram:send_morning_greeting')
            ->dailyAt('08:30')->when($todayIsWorking);

        $schedule->command('telegram_poll:start_at_work')
            ->dailyAt('08:30')->when($todayIsWorking);

        $schedule->command('telegram_poll:stop_at_work')
            ->dailyAt('08:45')->when($todayIsWorking);

        $schedule->command('telegram:send_users_who_go_on_vacation')
            ->dailyAt('09:00')->when($todayIsWorking);

        $schedule->command('telegram:send_users_who_celebrate_birthday')
            ->dailyAt('09:00')->when($todayIsWorking);

        $schedule->job(new ProcessConferencesBeforeStarting())->everyMinute();

        // Рестарт обработчика телеграмм обновлений
        $schedule->exec('supervisorctl restart laravel-telebot-polling')
            ->hourly()
            ->appendOutputTo(config('logging.channels.telegram.path'));

        // --------------------------------------------------------------------------

        $schedule->command('backup:run')->dailyAt('11:30');
        $schedule->command('backup:run')->dailyAt('15:30');
        $schedule->command('backup:clean')->dailyAt('04:00');
        $schedule->command('backup:monitor')->dailyAt('04:30');

        $schedule->command('file_service:database_management')->dailyAt('03:00');
        $schedule->command('file_service:filesystem_deletion')->dailyAt('03:30');
    }


    /**
     * Автоматическая регистрация команд
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
