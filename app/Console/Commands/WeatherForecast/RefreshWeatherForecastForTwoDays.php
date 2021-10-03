<?php

declare(strict_types=1);

namespace App\Console\Commands\WeatherForecast;

use Illuminate\Console\Command;
use App\Services\WeatherForecast\WeatherForecastService;


final class RefreshWeatherForecastForTwoDays extends Command
{

    protected $signature = 'weather_forecast:refresh_for_two_days';

    protected $description = "Обновляет прогноз погоды на два дня";


    public function handle(WeatherForecastService $service): int
    {
        $service->refreshWeatherForecastForTwoDays();
        $this->info('Прогноз погоды успешно обновлён');
        return 0;
    }
}
