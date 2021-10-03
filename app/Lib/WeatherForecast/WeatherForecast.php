<?php

declare(strict_types=1);

namespace App\Lib\WeatherForecast;

use App\Lib\WeatherForecast\Exceptions\WeatherForecastException;


interface WeatherForecast
{

    /**
     * Возвращает прогноз погоды на два дня. Сегодня и завтра
     *
     * @return DayInfo[] [Дата текущего дня, Дата следующего дня]
     * @throws WeatherForecastException в случае ошибки при получении прогноза погоды
     */
    public function getWeatherForecastForTwoDays(): array;
}
