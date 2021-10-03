<?php

declare(strict_types=1);

namespace App\Services\Telegram\Exceptions;


/**
 * Предупреждение. Не является программной ошибкой
 */
final class TelegramNoticeException extends TelegramException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
