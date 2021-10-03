<?php

declare(strict_types=1);

namespace App\Services\WeatherForecast;

use App\Lib\WeatherForecast\WeatherForecast;
use App\Models\WeatherForecast as WeatherForecastModel;


final class WeatherForecastService
{
    public function __construct(private WeatherForecast $weatherForecast)
    {}


    public function refreshWeatherForecastForTwoDays(): void
    {
        WeatherForecastModel::truncate();

        foreach ($this->weatherForecast->getWeatherForecastForTwoDays() as $dayInfo) {

            WeatherForecastModel::create([
                'day'         => $dayInfo->getDay()->format('Y-m-d'),
                'temperature' => $dayInfo->getTemperature(),
                'icon_code'   => $dayInfo->getIcon()->getCode(),
                'wind_speed'  => $dayInfo->getWindSpeed(),
            ]);
        }
    }
}
