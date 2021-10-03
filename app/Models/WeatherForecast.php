<?php

declare(strict_types=1);

namespace App\Models;

use App\Lib\WeatherForecast\Icon;


/**
 * @property-read Icon $icon
 * @mixin IdeHelperWeatherForecast
 */
final class WeatherForecast extends AppModel
{
    const UPDATED_AT = null;

    protected $casts = [
        'day'         => 'date',
        'temperature' => 'float',
        'wind_speed'  => 'float',
    ];

    protected $fillable = [
        'day',
        'temperature',
        'icon_code',
        'wind_speed'
    ];


    public function getIconAttribute(): Icon
    {
        return new Icon($this->getRequiredAttribute('icon_code'));
    }
}
