<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation;

use App\Lib\Csp\Exceptions\UnexpectedMessageException;
use App\Lib\Csp\SignatureValidation\MessageParsing\Glossary;


final class MessageTranslator
{

    /**
     * Переводчик должен обеспечивать перевод всех сообщений,
     * которые есть в методах {@see Glossary::signatureSuccess()} и {@see Glossary::signatureError()}
     */


    /**
     * Возвращает адаптированное для пользователя сообщение на основе результата проверки подписи
     *
     * @throws UnexpectedMessageException
     */
    public function translateSignatureMessage(string $message): string
    {
        if (str_icontains($message, "Signature's verified.")) {
            return 'Подпись действительна';
        }
        if (str_icontains($message, 'Error: Invalid algorithm specified.')) {
            return 'Подпись имеет недействительный алгоритм';
        }
        if (str_icontains($message, 'Error: Invalid Signature.')) {
            return 'Подпись не соответствует файлу';
        }
        throw new UnexpectedMessageException('Получен неизвестный результат проверки подписи', $message);
    }


    /**
     * Возвращает адаптированное для пользователя сообщение на основе результата проверки подписи (сертификата)
     *
     * @throws UnexpectedMessageException
     */
    public function translateCertificateMessage(string $message): string
    {
        if (str_icontains($message, "Signature's verified.")) {
            return 'Сертификат действителен';
        }
        if (str_icontains($message, 'This certificate or one of the certificates in the certificate chain is not time valid.')) {
            return 'Срок действия одного из сертификатов цепочки истек или еще не наступил';
        }
        if (str_icontains($message, 'Trust for this certificate or one of the certificates in the certificate chain has been revoked.')) {
            return 'Один из сертификатов в цепочке аннулирован';
        }
        if (str_icontains_any($message, ['Error: Invalid algorithm specified.', 'Error: Invalid Signature.'])) {
            return 'Сертификат не проверялся';
        }
        throw new UnexpectedMessageException('Получен неизвестный результат проверки сертификата', $message);
    }
}
