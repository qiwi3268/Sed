<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\OutputMessage;

use App\Lib\Csp\SignatureValidation\SignatureValidator;


interface OutputMessenger
{
    /**
     * Вызывается классом {@see SignatureValidator} непосредственно перед началом проверки
     */
    public function run(): void;

    /**
     * Возвращает сообщение проверки подписи с цепочкой сертификатов
     */
    public function getChainMessage(): string;

    /**
     * Возвращает сообщение проверки подписи без цепочки сертификатов
     */
    public function getNoChainMessage(): string;

    /**
     * Возвращает сообщение с информацией о сертификате
     */
    public function getCertificateInfoMessage(): string;
}
