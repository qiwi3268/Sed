<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\Entities;

use App\Lib\Csp\Certification\PersonalCertificate;
use Webmozart\Assert\Assert;


final class Signer
{

    /**
     * @param bool $signatureResult результат проверки подписи
     * @param string $signatureMessage адаптированное для пользователя сообщение о результате проверки подписи
     * @param PersonalCertificate $certificate личный сертификат подписанта
     * @param bool $certificateResult результат проверки сертификата
     * @param string $certificateMessage адаптированное для пользователя сообщение о результате проверки сертификата
     */
    public function __construct(
        private bool $signatureResult,
        private string $signatureMessage,
        private PersonalCertificate $certificate,
        private bool $certificateResult,
        private string $certificateMessage,
    ) {
        Assert::allNotEmpty([$signatureMessage, $certificateMessage]);
    }


    public function getCertificate(): PersonalCertificate { return $this->certificate; }

    public function getSignatureResult(): bool { return $this->signatureResult; }

    public function getSignatureMessage(): string { return $this->signatureMessage; }

    public function getCertificateResult(): bool { return $this->certificateResult; }

    public function getCertificateMessage(): string { return $this->certificateMessage; }
}
