<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation;

use App\Lib\Csp\Exceptions\NoSignersException;
use App\Lib\Csp\Exceptions\UnexpectedMessageException;

use App\Lib\Csp\SignatureValidation\OutputMessage\OutputMessenger;
use App\Lib\Csp\SignatureValidation\Entities\Signer;
use App\Lib\Csp\SignatureValidation\Entities\ValidationResult;
use App\Lib\Csp\SignatureValidation\MessageParsing\MessageParser;
use App\Lib\Csp\Certification\PersonalCertificate;
use App\Lib\Csp\SignatureValidation\MessageParsing\Glossary;
use App\Lib\Csp\Certification\MessageParser as CertificateInfoMessageParser;
use App\Lib\Csp\SignatureValidation\MessageParsing\ParsingResult\ParsingResult;


final class SignatureValidator
{
    private Glossary $glossary;
    private MessageTranslator $translator;


    public function __construct(private OutputMessenger $messenger)
    {
        $this->glossary = new Glossary();
        $this->translator = new MessageTranslator();
    }


    /**
     * Возвращает результат проверки подписи
     *
     * @return ValidationResult
     */
    public function validate(): ValidationResult
    {
        $this->messenger->run();

        $chainMessageParser = new MessageParser($this->messenger->getChainMessage());
        $noChainMessageParser = new MessageParser($this->messenger->getNoChainMessage());

        $chainResult = $chainMessageParser->parse();
        $noChainResult = $noChainMessageParser->parse();

        $this->validateParsingResultStructure($chainResult);
        $this->validateParsingResultStructure($noChainResult);

        if (!$chainResult->hasSignerPrototypes()) {
            throw new NoSignersException('В сообщении отсутствуют подписанты');
        }

        $certMessageParser = new CertificateInfoMessageParser($this->messenger->getCertificateInfoMessage());
        $certificates = $certMessageParser->parse();

        $signers = [];

        foreach ($chainResult->getSignerPrototypes() as $chainSignerPrototype) {

            $noChainSignerPrototype = $noChainResult->getIdenticalSigner($chainSignerPrototype);
            $certificate = $this->getSignerCertificate($chainSignerPrototype->getInfo(), $certificates);

            // Результат подписи от проверки без цепочки сертификатов
            // Результат сертификата от проверки с цепочкой сертификатов

            $signers[] = new Signer(
                $noChainResult->getResult(),
                $this->translator->translateSignatureMessage($noChainSignerPrototype->getMessage()),
                $certificate,
                $chainResult->getResult(),
                $this->translator->translateCertificateMessage($chainSignerPrototype->getMessage())
            );
        }
        return new ValidationResult($signers);
    }


    /**
     * Проверяет структуру объекта ParsingResult
     *
     * @throws UnexpectedMessageException
     */
    private function validateParsingResultStructure(ParsingResult $result): void
    {
        /*
         * -- Подписанты присутствуют --
         *
         * 1. Известное сообщение подписанта:
         *    без ошибок:
         *    - Signature's verified.
         *    с ошибками:
         *    - This certificate or one of the certificates in the certificate chain is not time valid.
         *    - Error: Invalid algorithm specified.
         *    - Trust for this certificate or one of the certificates in the certificate chain has been revoked.
         *    - Error: Invalid Signature.
         * ! 2. Техническая ошибка -> Неизвестное сообщение подписанта
         */

        /*
         * -- Отсутствуют подписанты --
         *
         * 1. Только errorCode
         *    Некорректные входные параметры
         *    При проверке открепленной подписи загружена НЕ открепленная подпись
         * 2. При проверке встроенной подписи загружена НЕ встроенная подпись, тогда commonError одно из:
         *    - Error: Invalid cryptographic message type.
         *    - Error: The parameter is incorrect.
         *    - Error: Incorrect BASE64 conversion.
         * ! 3. Техническая ошибка -> commonError не подошел ни под одно из выше перечисленных
         */

        if ($result->hasSignerPrototypes()) {

            foreach ($result->getSignerPrototypes() as $signerPrototype) {

                $message = $signerPrototype->getMessage();

                if ($this->glossary->signatureSuccess($message)) {
                    continue;
                }
                if ($this->glossary->signatureError($message)) {
                    continue;
                }
                throw new UnexpectedMessageException('Неизвестное сообщение подписанта', $message);
            }

        } else {

            $commonError = $result->getCommonError();

            if ($result->hasOnlyErrorCode() || $this->glossary->commonErrorAboutInvalidValidatedFile($commonError)) {
                return;
            }
            throw new UnexpectedMessageException('Неизвестный commonError', $commonError);
        }
    }


    /**
     * Возвращает сертификат, который соответствует информации о подписанте
     *
     * @param PersonalCertificate[] $certificates
     * @throws UnexpectedMessageException
     */
    private function getSignerCertificate(string $signerInfo, array $certificates): PersonalCertificate
    {
        foreach ($certificates as $certificate) {

            $fio = $certificate->getSubjectFio();

            $needles = $fio->hasMiddleName()
                ? [$fio->getLastName(), $fio->getFirstName(), $fio->getMiddleName()]
                : [$fio->getLastName(), $fio->getFirstName()];

            // Регистронезависимый поиск важен, т.к. информация о подписанте может быть большими буквами
            if (str_icontains_all($signerInfo, $needles)) {
                return $certificate;
            }
        }
        throw new UnexpectedMessageException('По информации о подписанте не найден сертификат', $signerInfo);
    }
}
