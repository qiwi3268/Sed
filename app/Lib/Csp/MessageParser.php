<?php

declare(strict_types=1);

namespace App\Lib\Csp;

use App\Lib\Csp\Exceptions\ParsingException;


class MessageParser
{
    /**
     * Код, соответствующий успешному выполнению команды
     */
    public const OK_ERROR_CODE = '0x00000000';

    protected const ERROR_CODE_PATTERN = '/\[ErrorCode:\s*(.+)]/i';


    /**
     * Возвращает код ошибки из сообщения
     *
     * @throws ParsingException
     */
    public function getErrorCode(string $message): string
    {
        if (!pm(self::ERROR_CODE_PATTERN, $message, $errorCode)) {
            throw new ParsingException('В сообщении отсутствует ErrorCode');
        }
        return $errorCode;
    }


    /**
     * Соответствует ли код ошибки успешному выполнению команды
     */
    public function isOkErrorCode(string $message): bool
    {
        return self::OK_ERROR_CODE == $this->getErrorCode($message);
    }
}
