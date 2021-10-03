<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\Commands;


interface SignatureValidationCommand
{
    /**
     * Возвращает абсолютный путь к файлу открепленной или встроенной подписи
     */
    public function getCryptographicFile(): string;

    /**
     * Возвращает команду проверки подписи с цепочкой сертификатов
     */
    public function getChainCommand(): array;

    /**
     * Возвращает команду проверки подписи без цепочки сертификатов
     */
    public function getNoChainCommand(): array;
}
