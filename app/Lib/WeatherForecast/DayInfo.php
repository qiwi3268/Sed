<?php

declare(strict_types=1);

namespace App\Lib\WeatherForecast;

use DateTimeImmutable;
use DateTimeInterface;


final class DayInfo
{
    private DateTimeImmutable $day;
    private float $temperature;
    private float $windSpeed;


    /**
     * @param float $temperature температура в градусах Цельсия
     * @param float $windSpeed скорость ветра в м/с
     */
    public function __construct(
        DateTimeInterface $day,
        float $temperature,
        private Icon $icon,
        float $windSpeed
    ) {
        $this->day = DateTimeImmutable::createFromInterface($day);
        $this->temperature = round($temperature, 1);
        $this->windSpeed = round($windSpeed, 1);
    }


    public function getDay(): DateTimeImmutable
    {
        return $this->day;
    }


    public function getTemperature(): float
    {
        return $this->temperature;
    }


    public function getIcon(): Icon
    {
        return $this->icon;
    }


    public function getWindSpeed(): float
    {
        return $this->windSpeed;
    }
}
