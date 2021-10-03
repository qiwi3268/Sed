<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\Entities;

use JsonException;
use App\Lib\Csp\Certification\PersonalCertificate;


final class ValidationResultFactory
{

    /**
     * @throws JsonException
     */
    public static function createFromJson(string $json): ValidationResult
    {
        $fromJson = json_decode($json, flags: JSON_THROW_ON_ERROR);

        $signers = [];

        foreach ($fromJson as $signer) {

            $certificate = $signer->certificate;

            $signers[] = new Signer(
                $signer->signature_result,
                $signer->signature_message,
                new PersonalCertificate(
                    (array) $certificate->issuer,
                    (array) $certificate->subject,
                    $certificate->serial,
                    $certificate->notValidBefore,
                    $certificate->notValidAfter,
                ),
                $signer->certificate_result,
                $signer->certificate_message
            );
        }
        return new ValidationResult($signers);
    }
}
