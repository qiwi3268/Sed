<?php

declare(strict_types=1);

namespace App\Services\Telegram\Exceptions;


/**
 * Программная ошибка
 */
final class TelegramErrorException extends TelegramException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
