<?php

declare(strict_types=1);

namespace App\Services\Files\ResponseSerialization;

use App\Lib\Csp\SignatureValidation\Entities\ValidationResult;
use JsonSerializable;


/**
 * Результат проверки подписи
 */
final class ValidationResultSerializer implements JsonSerializable
{
    private array $serialized;


    public function __construct(ValidationResult $validationResult)
    {
        $serialized = [
            'result'  => $validationResult->getResult(),
            'signers' => []
        ];

        foreach ($validationResult->getSigners() as $signer) {

            $certificate = $signer->getCertificate();
            $subjectFio = $certificate->getSubjectFio();

            $serialized['signers'][] = [
                'signatureResult'    => $signer->getSignatureResult(),
                'signatureMessage'   => $signer->getSignatureMessage(),
                'certificate' => [
                    'issuer'         => assoc_implode($certificate->getIssuer()),
                    'subject'        => assoc_implode($certificate->getSubject()),
                    'serial'         => $certificate->getSerial(),
                    'validRange'     => "с {$certificate->getNotValidBefore()} по {$certificate->getNotValidAfter()}"
                ],
                'certificateResult'  => $signer->getCertificateResult(),
                'certificateMessage' => $signer->getCertificateMessage(),
                'subject' => [
                    'lastName'   => $subjectFio->getLastName(),
                    'firstName'  => $subjectFio->getFirstName(),
                    'middleName' => $subjectFio->getMiddleName()
                ]
            ];
        }
        $this->serialized = $serialized;
    }


    public function jsonSerialize(): array
    {
        return $this->serialized;
    }
}
