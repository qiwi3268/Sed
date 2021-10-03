<?php

declare(strict_types=1);

namespace App\Models\Traits\Casts;

use LogicException;
use JsonException;

use Illuminate\Database\Eloquent\Model;
use App\Lib\Csp\SignatureValidation\Entities\ValidationResult;
use App\Lib\Csp\SignatureValidation\Entities\ValidationResultFactory;


trait ValidationResultCast
{

    /**
     * @throws JsonException
     */
    public function setValidationResultAttribute(ValidationResult $validationResult): void
    {
        /** @var Model $this */
        $forJson = [];

        foreach ($validationResult->getSigners() as $signer) {

            $certificate = $signer->getCertificate();

            $forJson[] = [
                'signature_result'    => $signer->getSignatureResult(),
                'signature_message'   => $signer->getSignatureMessage(),
                'certificate' => [
                    'issuer'          => $certificate->getIssuer(),
                    'subject'         => $certificate->getSubject(),
                    'serial'          => $certificate->getSerial(),
                    'notValidBefore'  => $certificate->getNotValidBefore(),
                    'notValidAfter'   => $certificate->getNotValidAfter()
                ],
                'certificate_result'  => $signer->getCertificateResult(),
                'certificate_message' => $signer->getCertificateMessage()
            ];
        }
        $this->attributes['validation_result'] = json_encode($forJson, flags: JSON_THROW_ON_ERROR);
    }


    /**
     * @throws JsonException
     * @throws LogicException
     */
    public function getValidationResultAttribute(): ?ValidationResult
    {
        /** @var Model $this */
        if (!array_key_exists('validation_result', $this->attributes)) {
            throw new LogicException('Отсутствуют необходимые атрибуты результата проверки подписи');
        }

        $attribute = $this->attributes['validation_result'];

        return is_null($attribute)
            ? null
            : ValidationResultFactory::createFromJson($attribute);
    }
}
