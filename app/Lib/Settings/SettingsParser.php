<?php

declare(strict_types=1);

namespace App\Lib\Settings;


interface SettingsParser
{
    /**
     * Возвращает схему настроек
     */
    public function getSchema(): array;
}
