<?php

declare(strict_types=1);

namespace App\Lib\Csp\Exceptions;


/**
 * Непредвиденный вывод утилиты
 */
final class UnexpectedMessageException extends CspException
{

    /**
     * @param string $unexpectedMessage непредвиденное сообщение.
     * Создано для обработчиков исключений, которые будут его логировать и т.д.
     */
    public function __construct(string $message, private string $unexpectedMessage, int $code = 0)
    {
        parent::__construct($message, $code);
    }


    public function getUnexpectedMessage(): string
    {
        return $this->unexpectedMessage;
    }
}
