<?php

declare(strict_types=1);

namespace App\Services\WeatherForecast;

use Exception;
use App\Lib\WeatherForecast\Exceptions\WeatherForecastException;

use App\Lib\WeatherForecast\Icon;
use App\Lib\WeatherForecast\DayInfo;
use App\Lib\WeatherForecast\WeatherForecast;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Forecast;
use Carbon\Carbon;


final class OpenWeatherMapAdapter implements WeatherForecast
{

    public function __construct(private OpenWeatherMap $openWeatherMap)
    {}


    /**
     * @throws WeatherForecastException
     */
    public function getWeatherForecastForTwoDays(): array
    {
        try {
            $iterator = $this->openWeatherMap->getWeatherForecast('Chelyabinsk', 'metric', 'ru', '', 2);
        } catch (Exception $e) {
            throw new WeatherForecastException('Ошибка при обращении к API', previous: $e);
        }

        $result = [];

        /** @var Forecast $forecast */
        foreach ($iterator as $forecast) {

            $date = new Carbon($forecast->time->from);

            if (($date->isToday() || $date->isTomorrow()) && $date->hour == 12) {

                $result[] = new DayInfo(
                    $date,
                    $forecast->temperature->now->getValue(), // Средняя температура
                    $this->getIcon($forecast->weather->icon),
                    $forecast->wind->speed->getValue()
                );
            }
        }
        // Прогноз погоды может отсутствовать, если делать запрос поздно
        if (count($result) != 2) {
            throw new WeatherForecastException('Ошибка при получении текущего и следующего дня');
        }
        return $result;
    }


    private function getIcon(string $code): Icon
    {
        return match ($code) {
            '01d', '01n' => new Icon(Icon::CODE['clear_sky']),
            '02d', '02n' => new Icon(Icon::CODE['few_clouds']),
            '03d', '03n',
            '04d', '04n' => new Icon(Icon::CODE['clouds']),
            '09d', '09n',
            '10d', '10n' => new Icon(Icon::CODE['rain']),
            '11d', '11n' => new Icon(Icon::CODE['thunderstorm']),
            '13d', '13n' => new Icon(Icon::CODE['snow']),
            '50d', '50n' => new Icon(Icon::CODE['mist']),
            default      => new Icon(Icon::CODE['fallback'])
        };
    }
}
