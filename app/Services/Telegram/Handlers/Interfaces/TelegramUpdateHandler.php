<?php

declare(strict_types=1);

namespace App\Services\Telegram\Handlers\Interfaces;

use WeStacks\TeleBot\Interfaces\UpdateHandler;


abstract class TelegramUpdateHandler extends UpdateHandler
{
    use TelegramHandlerTrait;
}
