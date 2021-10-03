<?php

declare(strict_types=1);

namespace App\Services\Telegram\UseCases;

use LogicException;

use App\Models\WeatherForecast;
use App\Services\Telegram\CompanyTelegram;


final class MorningGreetingUseCase
{
    public function __construct(private CompanyTelegram $companyTelegram)
    {}


    public function sendMessage(): void
    {
        $forecasts = WeatherForecast::whereIn('day', [now(), now()->addDay()])
            ->orderBy('day')
            ->get(['temperature', 'icon_code', 'wind_speed']);

        if ($forecasts->count() != 2) {
            throw new LogicException('Отсутствует прогноз погоды на два дня');
        }

        $messages = [];

        foreach ($forecasts as $forecast) {

            $day = count($messages) == 0 ? 'Сегодня' : 'Завтра ';

            $temperature = ((float) $forecast->temperature > 0 ? '+' : '') . "{$forecast->temperature}°";

            $windSpeed = $forecast->wind_speed . ' м/с';

            $messages[] = "$day   {$forecast->icon->getEmoji()}   $temperature   $windSpeed";
        }

        $this->companyTelegram->sendMessageToMainChat(
            <<<MD
Приветствую вас, уважаемые коллеги!

За окном:
$messages[0]
$messages[1]

Желаю вам хорошего настроения и продуктивного рабочего дня.
MD,
            ['parse_mode' => 'markdown']
        );
    }
}
