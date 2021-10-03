<?php

declare(strict_types=1);

namespace App\Lib\WeatherForecast;

use InvalidArgumentException;


final class Icon
{
    public const CODE = [
        'clear_sky'    => 'clear_sky',    // чистое небо
        'few_clouds'   => 'few_clouds',   // небольшая облачность
        'clouds'       => 'clouds',       // облачность
        'rain'         => 'rain',         // дождь
        'thunderstorm' => 'thunderstorm', // гроза
        'snow'         => 'snow',         // снег
        'mist'         => 'mist',         // туман
        'fallback'     => 'fallback'      // нужная иконка отсутствует
    ];


    public const EMOJI = [
        self::CODE['clear_sky']    => '☀',
        self::CODE['few_clouds']   => '⛅',
        self::CODE['clouds']       => '☁',
        self::CODE['rain']         => '🌧',
        self::CODE['thunderstorm'] => '⛈',
        self::CODE['snow']         => '❄',
        self::CODE['mist']         => '🌫',
        self::CODE['fallback']     => '☄'
    ];


    /**
     * @throws InvalidArgumentException
     */
    public function __construct(private string $code)
    {
        if (!in_array($code, self::CODE)) {
            throw new InvalidArgumentException("Некорректный код иконки: $code");
        }
    }


    public function getCode(): string
    {
        return $this->code;
    }


    public function getEmoji(): string
    {
        return self::EMOJI[$this->code];
    }
}
